<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Content;
use Carbon\Carbon;

class DisplayPublicController extends Controller
{
    // ── Halaman TV/Videowall ────────────────────────────────────────────────
    public function show(string $token)
    {
        $display = Display::where('device_token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        return view('display.screen', compact('display'));
    }

    // ── API: Ambil konten aktif saat ini ────────────────────────────────────
    public function activeContent(string $token)
    {
        $display = Display::where('device_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$display) {
            return response()->json(['status' => false, 'message' => 'Display not found'], 404);
        }

        $now         = Carbon::now();
        $today       = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Auto-expire konten yang sudah lewat
        Content::where('status', 'active')
            ->where('display_id', $display->id)
            ->where(function ($q) use ($today, $currentTime) {
                $q->where('display_date', '<', $today)
                  ->orWhere(function ($q2) use ($today, $currentTime) {
                      $q2->where('display_date', $today)
                         ->where('end_time', '<', $currentTime);
                  });
            })->update(['status' => 'expired']);

        // Auto-aktifkan draft yang masuk jadwal
        Content::where('status', 'draft')
            ->where('display_id', $display->id)
            ->where('display_date', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->update(['status' => 'active']);

        // Ambil konten aktif
        $content = Content::where('display_id', $display->id)
            ->where('status', 'active')
            ->where('display_date', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->with('details')
            ->first();

        if (!$content) {
            return response()->json(['status' => false, 'message' => 'Standby Mode']);
        }

        // Normalisasi asset URL agar tahan ganti host/port (localhost vs 127.0.0.1:8000)
        $customElements = $content->custom_elements ?? [];
        if (isset($customElements['bg']['url'])) {
            if (preg_match('#/storage/(.+)#', $customElements['bg']['url'], $m)) {
                if (($customElements['bg']['type'] ?? '') === 'video') {
                    // Gunakan dedicated streaming endpoint agar mendukung HTTP 206 Range seeking
                    $customElements['bg']['url'] = route('media.stream', ['path' => $m[1]]);
                } else {
                    $customElements['bg']['url'] = asset('storage/' . $m[1]);
                }
            }
        }
        if (isset($customElements['logos']) && is_array($customElements['logos'])) {
            foreach ($customElements['logos'] as &$l) {
                if (isset($l['url']) && preg_match('#/storage/(.+)#', $l['url'], $m)) {
                    $l['url'] = asset('storage/' . $m[1]);
                }
            }
        }

        return response()->json([
            'status'      => true,
            'server_time' => microtime(true),
            'data'        => [
                'id'              => $content->id,
                'title'           => $content->title,
                'custom_elements' => $customElements,
                'details'         => $content->details->map(fn($d) => [
                    'content'   => $d->content_text,
                    'color'     => $d->color,
                    'positions' => $d->positions,
                ]),
                'display_date' => $content->display_date->format('Y-m-d'),
                'end_time'     => $content->end_time,
                'updated_at'   => $content->updated_at ? $content->updated_at->timestamp : time(),
            ],
        ]);
    }

    // ── Streaming Media dengan HTTP 206 Range Request (Sync Multi-Monitor) ────
    public function streamMedia(string $path)
    {
        $cleanPath = str_replace(['..', '\\'], ['', '/'], $path);
        $fullPath  = storage_path('app/public/' . $cleanPath);

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            $publicPath = public_path('storage/' . $cleanPath);
            if (file_exists($publicPath) && is_file($publicPath)) {
                $fullPath = $publicPath;
            } else {
                abort(404);
            }
        }

        $mimeType = mime_content_type($fullPath) ?: 'video/mp4';

        return response()->file($fullPath, [
            'Content-Type'  => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=2592000, immutable',
        ]);
    }
}