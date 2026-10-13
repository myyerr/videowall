<?php

namespace App\Http\Controllers;

use App\Models\Display;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DisplayController extends Controller
{
    public function index()
    {
        $displays = Display::latest()->get();

        return view('displays.index', compact('displays'));
    }

    public function create()
    {
        return view('displays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['device_token'] = Str::random(32);

        Display::create($validated);

        return redirect()->route('displays.index')
            ->with('success', 'Display berhasil ditambahkan.');
    }

    public function show(Display $display)
    {
        $display->load('contents');

        return view('displays.show', compact('display'));
    }

    public function edit(Display $display)
    {
        return view('displays.edit', compact('display'));
    }

    public function update(Request $request, Display $display)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $display->update($validated);

        return redirect()->route('displays.index')
            ->with('success', 'Display berhasil diperbarui.');
    }

    public function destroy(Display $display)
    {
        $display->delete();

        return redirect()->route('displays.index')
            ->with('success', 'Display berhasil dihapus.');
    }

    // Regenerate token kalau device_token perlu di-reset (misal kebocoran token)
    public function regenerateToken(Display $display)
    {
        $display->update(['device_token' => Str::random(32)]);

        return redirect()->route('displays.index')
            ->with('success', 'Token display berhasil diperbarui.');
    }
}