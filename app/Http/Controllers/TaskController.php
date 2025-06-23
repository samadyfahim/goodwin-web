<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        \Log::debug('TaskController@store request', $request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,done',
            'users' => 'array',
            'users.*' => 'integer|exists:users,id',
        ]);
        $task = $project->tasks()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);
        $task->users()->sync($validated['users'] ?? []);
        $task->load('creator', 'users');
        return back()->with('success', 'Task created!');
    }

    public function update(Request $request, Project $project, Task $task)
    {
        \Log::debug('TaskController@update request', $request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,done',
            'users' => 'array',
            'users.*' => 'integer|exists:users,id',
        ]);
        $task->update($validated);
        $task->users()->sync($validated['users'] ?? []);
        $task->load('creator', 'users');
        return back()->with('success', 'Task updated!');
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task deleted!');
    }

    public function removeUser(Project $project, Task $task, \App\Models\User $user)
    {
        $task->users()->detach($user->id);
        return back()->with('success', 'User removed from task!');
    }
} 