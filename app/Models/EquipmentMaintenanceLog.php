<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMaintenanceLog extends Model
{
    protected $fillable = [
        'equipment_id',
        'type',
        'status',
        'description',
        'cost',
        'scheduled_date',
        'completion_date',
        'performed_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2'
    ];

    /**
     * Get the equipment that owns the maintenance log.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who performed the maintenance.
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope a query to only include scheduled maintenance.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include in-progress maintenance.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed maintenance.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include maintenance tasks.
     */
    public function scopeMaintenance($query)
    {
        return $query->where('type', 'maintenance');
    }

    /**
     * Scope a query to only include repair tasks.
     */
    public function scopeRepairs($query)
    {
        return $query->where('type', 'repair');
    }
} 