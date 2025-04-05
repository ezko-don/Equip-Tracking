<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'due_date',
        'priority',
        'status',
        'equipment_id',
        'assigned_to',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'equipment_id' => 'integer',
        'assigned_to' => 'integer',
        'created_by' => 'integer'
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'medium',
        'description' => ''
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 