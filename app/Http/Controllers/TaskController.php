<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller for managing tasks within projects.
 * Handles creation, updating, deletion, and user assignment for tasks.
 */
class TaskController extends Controller
{
    /**
     * Store a newly created task for a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Project $project)
    {
        \Log::debug('TaskController@store request', $request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,done',
            'deadline' => 'nullable|date',
            'users' => 'array',
            'users.*' => 'integer|exists:users,id',
        ]);
        // Create the task and assign the creator
        $task = $project->tasks()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);
        // Attach users to the task if provided
        $task->users()->sync($validated['users'] ?? []);
        $task->load('creator', 'users');
        return back()->with('success', 'Task created!');
    }

    /**
     * Update the specified task for a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project, Task $task)
    {
        \Log::debug('TaskController@update request', $request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,done',
            'deadline' => 'nullable|date',
            'users' => 'array',
            'users.*' => 'integer|exists:users,id',
        ]);
        // Update the task and sync users
        $task->update($validated);
        $task->users()->sync($validated['users'] ?? []);
        $task->load('creator', 'users');
        return back()->with('success', 'Task updated!');
    }

    /**
     * Remove the specified task from a project.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task deleted!');
    }

    /**
     * Remove a user from a task.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Task  $task
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeUser(Project $project, Task $task, \App\Models\User $user)
    {
        $task->users()->detach($user->id);
        return back()->with('success', 'User removed from task!');
    }
} 