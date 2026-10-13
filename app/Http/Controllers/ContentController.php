<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentDetail;
use App\Models\Display;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $this->autoUpdateStatuses();

        $query = Content::with(['display', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('display_id')) {
            $query->where('display_id', $request->display_id);
        }

        $contents = $query->paginate(15)->withQueryString();
        $displays = Display::where('status', 'active')->get();

        // Gantt data
        $fromParam = $request->query('from');
        $toParam   = $request->query('to');

        if (!$fromParam || !$toParam) {
            $dow        = (int) date('w');
            $daysToMon  = ($dow === 0) ? 6 : ($dow - 1);
            $filterFrom = date('Y-m-d', strtotime('-' . $daysToMon . ' days'));
            $filterTo   = date('Y-m-d', strtotime($filterFrom . ' +13 days'));
        } else {
            $filterFrom = date('Y-m-d', strtotime($fromParam));
            $filterTo   = date('Y-m-d', strtotime($toParam));
        }

        $fromTs     = strtotime($filterFrom);
        $toTs       = strtotime($filterTo);
        $rangeDates = [];
        for ($ts = $fromTs; $ts <= $toTs; $ts = strtotime('+1 day', $ts)) {
            $rangeDates[] = date('Y-m-d', $ts);
        }

        $ganttRows = Content::whereBetween('display_date', [$filterFrom, $filterTo])
            ->orderBy('created_at')
            ->get();

        $ganttData = [];
        foreach ($ganttRows as $row) {
            $ganttData[$row->display_date->format('Y-m-d')][] = $row;
        }

        // Stats
        $today = Carbon::today()->toDateString();
        $stats = [
            'active'  => Content::where('status', 'active')->count(),
            'draft'   => Content::where('status', 'draft')->count(),
            'expired' => Content::where('status', 'expired')->count(),
            'today'   => Content::where('display_date', $today)->count(),
        ];

        return view('contents.index', compact(
            'contents',
            'displays',
            'ganttData',
            'filterFrom',
            'filterTo',
            'rangeDates',
            'stats'
        ));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create()
    {
        $displays = Display::where('status', 'active')->get();
        $logos    = Asset::where('type', 'logo')->latest()->get();
        $videos   = Asset::where('type', 'video_background')->latest()->get();
        $images   = Asset::where('type', 'image_background')->latest()->get();

        return view('contents.create', compact('displays', 'logos', 'videos', 'images'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'display_id'   => 'required|exists:displays,id',
            'title'        => 'required|string|max:255',
            'display_date' => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required',
        ]);

        $displayDate = $request->display_date;
        $startTime   = date('H:i:s', strtotime($request->start_time));
        $endTime     = date('H:i:s', strtotime($request->end_time));

        if ($startTime >= $endTime) {
            return back()->withInput()->withErrors([
                'start_time' => 'Jam mulai harus lebih awal dari jam selesai.',
            ])->with('error', 'Jam mulai harus lebih awal dari jam selesai.');
        }

        if ($this->hasConflict($request->display_id, $displayDate, $startTime, $endTime)) {
            return back()->withInput()->withErrors([
                'start_time' => 'Jadwal bentrok dengan jadwal lain yang sudah ada pada display ini.',
            ])->with('error', 'Jadwal bentrok dengan jadwal lain yang sudah ada pada display ini.');
        }

        DB::transaction(function () use ($request, $displayDate, $startTime, $endTime) {
            $customElements = null;
            if ($request->filled('custom_elements_json')) {
                $decoded = json_decode($request->custom_elements_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $customElements = $decoded;
                }
            }

            $content = Content::create([
                'display_id'      => $request->display_id,
                'title'           => $request->title,
                'custom_elements' => $customElements,
                'status'          => Content::computeStatus($displayDate, $startTime, $endTime),
                'display_date'    => $displayDate,
                'start_time'      => $startTime,
                'end_time'        => $endTime,
                'created_by'      => Auth::id(),
            ]);

            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $index => $detail) {
                    if (!empty($detail['content_text'])) {
                        ContentDetail::create([
                            'content_id'   => $content->id,
                            'content_text' => $detail['content_text'],
                            'color'        => $detail['color'] ?? '#FFFF00',
                            'positions'    => $detail['positions'] ?? null,
                            'sort_order'   => $index,
                        ]);
                    }
                }
            }
        });

        $this->safeBroadcast($request->display_id);

        return redirect()->route('contents.index')
            ->with('success', 'Request videowall berhasil disimpan!');
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    public function edit(Content $content)
    {
        $content->load('details');
        $displays = Display::where('status', 'active')->get();
        $logos    = Asset::where('type', 'logo')->latest()->get();
        $videos   = Asset::where('type', 'video_background')->latest()->get();
        $images   = Asset::where('type', 'image_background')->latest()->get();

        return view('contents.edit', compact('content', 'displays', 'logos', 'videos', 'images'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, Content $content)
    {
        $request->validate([
            'display_id'   => 'required|exists:displays,id',
            'title'        => 'required|string|max:255',
            'display_date' => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required',
        ]);

        $displayDate = $request->display_date;
        $startTime   = date('H:i:s', strtotime($request->start_time));
        $endTime     = date('H:i:s', strtotime($request->end_time));

        if ($startTime >= $endTime) {
            return back()->withInput()->withErrors([
                'start_time' => 'Jam mulai harus lebih awal dari jam selesai.',
            ])->with('error', 'Jam mulai harus lebih awal dari jam selesai.');
        }

        if ($this->hasConflict($request->display_id, $displayDate, $startTime, $endTime, $content->id)) {
            return back()->withInput()->withErrors([
                'start_time' => 'Jadwal bentrok dengan jadwal lain yang sudah ada pada display ini.',
            ])->with('error', 'Jadwal bentrok dengan jadwal lain yang sudah ada pada display ini.');
        }

        DB::transaction(function () use ($request, $content, $displayDate, $startTime, $endTime) {
            $customElements = $content->custom_elements;
            if ($request->filled('custom_elements_json')) {
                $decoded = json_decode($request->custom_elements_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $customElements = $decoded;
                }
            }

            $content->update([
                'display_id'      => $request->display_id,
                'title'           => $request->title,
                'custom_elements' => $customElements,
                'status'          => Content::computeStatus($displayDate, $startTime, $endTime),
                'display_date'    => $displayDate,
                'start_time'      => $startTime,
                'end_time'        => $endTime,
            ]);

            $content->details()->delete();
            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $index => $detail) {
                    if (!empty($detail['content_text'])) {
                        ContentDetail::create([
                            'content_id'   => $content->id,
                            'content_text' => $detail['content_text'],
                            'color'        => $detail['color'] ?? '#FFFF00',
                            'positions'    => $detail['positions'] ?? null,
                            'sort_order'   => $index,
                        ]);
                    }
                }
            }
        });

        $this->safeBroadcast($request->display_id);

        return redirect()->route('contents.index')
            ->with('success', 'Request videowall berhasil diperbarui!');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(Content $content)
    {
        $displayId = $content->display_id;
        $content->delete();

        $this->safeBroadcast($displayId);

        if (request()->expectsJson()) {
            return response()->json(['status' => true, 'message' => 'Berhasil dihapus.']);
        }

        return redirect()->route('contents.index')
            ->with('success', 'Request berhasil dihapus.');
    }

    // ── EXPIRE ────────────────────────────────────────────────────────────────
    public function expire(Content $content)
    {
        $content->update(['status' => 'expired']);
        $this->safeBroadcast($content->display_id);
        return response()->json(['status' => true, 'message' => 'Status diubah ke expired.']);
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────
    private function hasConflict(int $displayId, string $date, string $start, string $end, ?int $excludeId = null): bool
    {
        $query = Content::whereIn('status', ['draft', 'active'])
            ->where('display_id', $displayId)
            ->where('display_date', $date)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_time', '<=', $start)
                         ->where('end_time', '>=', $end);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function autoUpdateStatuses(): void
    {
        $now         = Carbon::now();
        $today       = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Expire yang sudah lewat
        Content::where('status', 'active')
            ->where(function ($q) use ($today, $currentTime) {
                $q->where('display_date', '<', $today)
                  ->orWhere(function ($q2) use ($today, $currentTime) {
                      $q2->where('display_date', $today)
                         ->where('end_time', '<', $currentTime);
                  });
            })
            ->update(['status' => 'expired']);

        // Aktifkan draft yang masuk jadwal
        Content::where('status', 'draft')
            ->where('display_date', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->update(['status' => 'active']);
    }

    private function safeBroadcast(int $displayId): void
    {
        try {
            broadcast(new \App\Events\DisplayUpdated($displayId));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Broadcast failed for display $displayId: " . $e->getMessage());
        }
    }
}
