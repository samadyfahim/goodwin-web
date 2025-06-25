<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Comment model representing a comment entity on a note.
 * Handles relationships to note and creator.
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'content',
        'created_by',
    ];

    /**
     * Get the note that owns this comment.
     *
     * @return BelongsTo
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    /**
     * Get the user who created this comment.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 