<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller for managing projects and related dashboard data.
 * Handles CRUD operations and data aggregation for project views.
 */
class ProjectController extends Controller
{
    /**
     * Display a listing of all projects for the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Eager load related models for performance
        $projects = Project::with(['creator', 'customers', 'tasks', 'users'])
            ->orderBy('created_at', 'desc')
            ->get();
        $authUserId = auth()->id();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'authUserId' => $authUserId,
        ]);
    }

    /**
     * Show the form for creating a new project.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Projects/Create');
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,active,completed,delayed',
            'deadline' => 'nullable|date|after:today',
        ]);

        $project = Project::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project with all related data.
     *
     * @param  \App\Models\Project  $project
     * @return \Inertia\Response
     */
    public function show(Project $project)
    {
        // Eager load all related data for the project detail view
        $project->load([
            'creator',
            'customers',
            'tasks.users',
            'tasks.creator',
            'users',
            'files',
            'notes.comments.creator',
            'notes.creator',
        ]);
        // Map tasks to ensure users are arrays for frontend
        $project->tasks->transform(function ($task) {
            $task->users = $task->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            })->values()->all();
            return $task;
        });
        // Flatten comments for easier frontend use
        $comments = $project->notes->flatMap(function($note) {
            return $note->comments;
        })->map(function($comment) {
            return [
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
            ];
        })->values()->all();
        return Inertia::render('Projects/Show', [
            'project' => $project,
            'comments' => $comments,
        ]);
    }

    /**
     * Update the specified project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,active,completed,delayed',
        ]);
        $project->update($validated);
        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project and all related data deleted successfully!');
    }

    /**
     * Get dashboard data for the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function dashboardData()
    {
        $user = auth()->user();
        // Get all tasks assigned to the user, sorted by deadline (soonest first, nulls last), then by created_at
        $tasks = \App\Models\Task::with(['project'])
            ->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC')
            ->orderBy('created_at', 'asc')
            ->get();
        // Get all projects the user is a member of
        $projects = $user->projects()->with('creator')->orderBy('created_at', 'desc')->get();
        return inertia('Dashboard', [
            'upcomingTasks' => $tasks,
            'teamProjects' => $projects,
        ]);
    }
} 