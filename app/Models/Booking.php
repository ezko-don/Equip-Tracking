<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'event_name',
        'location',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'notes',
        'return_condition',
        'return_notes',
        'returned_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'returned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
        $this->equipment->update(['status' => 'unavailable']);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
        $this->equipment->update(['status' => 'available']);
    }

    public function cancel()
    {
        DB::transaction(function () {
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            // Update equipment status to available
            $this->equipment->update([
                'status' => 'available'
            ]);
        });
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isDeletable(): bool
    {
        return in_array($this->status, ['pending', 'rejected', 'cancelled']);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getFormattedStartDate()
    {
        return $this->start_time instanceof \Carbon\Carbon 
            ? $this->start_time->format('M d, Y')
            : Carbon::parse($this->start_time)->format('M d, Y');
    }

    public function getFormattedStartTime()
    {
        return $this->start_time instanceof \Carbon\Carbon 
            ? $this->start_time->format('H:i')
            : Carbon::parse($this->start_time)->format('H:i');
    }

    public function getFormattedEndTime()
    {
        return $this->end_time instanceof \Carbon\Carbon 
            ? $this->end_time->format('H:i')
            : Carbon::parse($this->end_time)->format('H:i');
    }
}
