<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectTeamController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_ids' => 'sometimes|array',
            'user_ids.*' => 'exists:users,id',
            'user_id' => 'sometimes|exists:users,id', // For backward compatibility
        ]);

        $userIds = [];
        
        // Handle both single user_id and array of user_ids
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $userIds = $request->user_ids;
        } elseif ($request->has('user_id')) {
            $userIds = [$request->user_id];
        }

        if (empty($userIds)) {
            return back()->withErrors(['user_ids' => 'No team members selected.']);
        }

        $project->users()->syncWithoutDetaching($userIds);
        
        $count = count($userIds);
        $message = $count === 1 ? 'Team member added!' : "{$count} team members added!";
        
        return back()->with('success', $message);
    }

    public function destroy(Project $project, User $user)
    {
        $project->users()->detach($user->id);
        return back()->with('success', 'Team member removed!');
    }
} 