<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectFileController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);
        $uploaded = $request->file('file')->store('project_files');
        $file = $project->files()->create([
            'filename' => $request->file('file')->getClientOriginalName(),
            'filepath' => $uploaded,
            'created_by' => auth()->id(),
        ]);
        return back()->with('success', 'File uploaded!');
    }

    public function destroy(Project $project, File $file)
    {
        Storage::delete($file->filepath);
        $file->delete();
        return back()->with('success', 'File deleted!');
    }

    public function download(Project $project, File $file)
    {
        return Storage::download($file->filepath, $file->filename);
    }

    public function view(Project $project, File $file)
    {
        $mime = Storage::mimeType($file->filepath);
        return response()->file(Storage::path($file->filepath), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $file->filename . '"',
        ]);
    }
} 