<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'deadline',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'created_by' => 'integer',
    ];

    /**
     * Get the project that owns this task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created this task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users assigned to this task.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    /**
     * Get the files for this task.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
} 