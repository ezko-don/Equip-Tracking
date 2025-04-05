<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'equipment_maintenance_logs';

    protected $fillable = [
        'equipment_id',
        'type',
        'status',
        'description',
        'cost',
        'scheduled_date',
        'completion_date',
        'performed_by',
        'receipt_path'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
} 