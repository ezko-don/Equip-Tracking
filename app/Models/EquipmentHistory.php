<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'user_id',
        'action',
        'status',
        'condition',
        'notes',
    ];

    /**
     * Get the equipment that owns the history record.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getConditionLabelAttribute()
    {
        return match($this->condition) {
            'new' => 'New',
            'good' => 'Good',
            'damaged' => 'Damaged',
            'under_repair' => 'Under Repair',
            default => ucfirst($this->condition)
        };
    }
}
