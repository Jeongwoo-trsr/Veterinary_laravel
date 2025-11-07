<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    // Show all announcements
    public function index(Request $request)
    {
        $query = Announcement::with('creator')->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    // Show create form
    public function create()
    {
        return view('announcements.create');
    }

    // Store new announcement
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['created_by'] = Auth::id();

        Announcement::create($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    // Show single announcement
    public function show(Announcement $announcement)
    {
        $announcement->load('creator');
        return view('announcements.show', compact('announcement'));
    }

    // Show edit form
    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    // Update announcement
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    // Delete announcement
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}