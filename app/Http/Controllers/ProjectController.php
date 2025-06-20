<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::with(['creator', 'customers', 'tasks', 'users'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load([
            'creator',
            'customers',
            'tasks.users',
            'tasks.creator',
            'users',
            'files',
            'notes'
        ]);

        return Inertia::render('Projects/Show', [
            'project' => $project
        ]);
    }
} 