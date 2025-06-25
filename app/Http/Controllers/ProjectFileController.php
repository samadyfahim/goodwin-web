<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\User;

/**
 * Controller for managing project files.
 * Handles file upload, deletion, download, and viewing.
 */
class ProjectFileController extends Controller
{
    /**
     * Store a newly uploaded file for a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);
        // Store the uploaded file
        $uploaded = $request->file('file')->store('project_files');
        $file = $project->files()->create([
            'filename' => $request->file('file')->getClientOriginalName(),
            'filepath' => $uploaded,
            'created_by' => auth()->id(),
        ]);
        return back()->with('success', 'File uploaded!');
    }

    /**
     * Remove the specified file from a project.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project, File $file)
    {
        Storage::delete($file->filepath);
        $file->delete();
        return back()->with('success', 'File deleted!');
    }

    /**
     * Download the specified file from a project.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\File  $file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Project $project, File $file)
    {
        return Storage::download($file->filepath, $file->filename);
    }

    /**
     * View the specified file inline in the browser.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\File  $file
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function view(Project $project, File $file)
    {
        $mime = Storage::mimeType($file->filepath);
        return response()->file(Storage::path($file->filepath), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $file->filename . '"',
        ]);
    }

    /**
     * Display a listing of all uploaded files.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $files = \App\Models\File::with(['project', 'creator'])->orderByDesc('created_at')->get();
        return Inertia::render('UploadedFiles', [
            'files' => $files
        ]);
    }
} 