<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Note;
use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        $note = $project->notes()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);
        $note->load('creator');
        return back()->with('success', 'Note added!');
    }

    public function update(Request $request, Project $project, Note $note)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        $note->update($validated);
        $note->load('creator');
        return back()->with('success', 'Note updated!');
    }

    public function destroy(Project $project, Note $note)
    {
        $note->delete();
        return back()->with('success', 'Note deleted!');
    }

    public function commentsStore(Request $request, Project $project, Note $note)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        $comment = $note->comments()->create([
            'content' => $validated['content'],
            'created_by' => auth()->id(),
        ]);
        $comment->load('creator');
        if ($request->wantsJson()) {
            return response()->json([
                'id' => $comment->id,
                'note_id' => $comment->note_id,
                'content' => $comment->content,
                'created_by' => $comment->created_by,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'creator' => $comment->creator ? [
                    'id' => $comment->creator->id,
                    'name' => $comment->creator->name,
                    'email' => $comment->creator->email,
                ] : null,
            ]);
        }
        return back()->with('success', 'Comment added!');
    }
} 