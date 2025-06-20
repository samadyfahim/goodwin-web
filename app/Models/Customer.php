<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'postcode',
        'phone',
        'address',
        'created_by',
    ];

    /**
     * Get the user who created this customer.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the projects associated with this customer.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_customer');
    }
} 