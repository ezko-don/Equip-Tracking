<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'description',
        'maintenance_date',
        'cost'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
} 