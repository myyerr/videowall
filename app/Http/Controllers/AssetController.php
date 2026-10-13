<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index()
    {
        $logos  = Asset::where('type', 'logo')->latest()->get();
        $videos = Asset::where('type', 'video_background')->latest()->get();
        $images = Asset::where('type', 'image_background')->latest()->get();

        return view('assets.index', compact('logos', 'videos', 'images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:logo,video_background,image_background',
            'file' => 'required|file|max:102400', // max 100MB
        ]);

        $type = $request->type;
        $file = $request->file('file');

        if ($type === 'logo') {
            $request->validate(['file' => 'mimes:png,jpg,jpeg,svg,webp']);
        } elseif ($type === 'image_background') {
            $request->validate(['file' => 'mimes:jpg,jpeg,png,webp']);
        } else {
            $request->validate(['file' => 'mimes:mp4,webm,mov']);
        }

        $path = $file->store('assets/' . $type, 'public');

        Asset::create([
            'name'        => $request->name,
            'type'        => $type,
            'file_path'   => $path,
            'mime_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            $asset = Asset::where('file_path', $path)->first();
            return response()->json([
                'status' => true,
                'asset'  => [
                    'id'   => $asset->id,
                    'name' => $asset->name,
                    'url'  => $asset->url,
                    'type' => $asset->type,
                ],
            ]);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset berhasil diupload.');
    }

    // AJAX upload langsung dari form create
    public function ajaxUpload(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:logo,video_background,image_background',
            'file' => 'required|file|max:102400', // max 100MB
        ]);

        $type = $request->type;
        $file = $request->file('file');

        if ($type === 'logo') {
            $request->validate(['file' => 'mimes:png,jpg,jpeg,svg,webp']);
        } elseif ($type === 'image_background') {
            $request->validate(['file' => 'mimes:jpg,jpeg,png,webp']);
        } else {
            $request->validate(['file' => 'mimes:mp4,webm,mov']);
        }

        $path = $file->store('assets/' . $type, 'public');

        $asset = Asset::create([
            'name'        => $request->name,
            'type'        => $type,
            'file_path'   => $path,
            'mime_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => true,
            'asset'  => [
                'id'   => $asset->id,
                'name' => $asset->name,
                'url'  => asset('storage/' . $asset->file_path),
                'type' => $asset->type,
            ],
        ]);
    }

    public function destroy(Asset $asset)
    {
        Storage::disk('public')->delete($asset->file_path);
        $asset->delete();

        if (request()->expectsJson()) {
            return response()->json(['status' => true, 'message' => 'Asset dihapus.']);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset berhasil dihapus.');
    }
}