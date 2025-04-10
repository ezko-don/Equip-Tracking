<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Notifications\EquipmentConditionUpdated;
use Carbon\Carbon;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'condition',
        'image',
        'category_id',
        'is_active',
        'availability_status',
        'quantity'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_IN_USE = 'in_use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RETIRED = 'retired';

    // Condition constants
    const CONDITION_NEW = 'new';
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';
    const CONDITION_POOR = 'poor';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            if (empty($equipment->slug)) {
                $equipment->slug = Str::slug($equipment->name);
            }
        });

        static::updating(function ($equipment) {
            if ($equipment->isDirty('name')) {
                $equipment->slug = Str::slug($equipment->name);
            }
        });
    }

    /**
     * Get the category that owns the equipment.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE,
            self::STATUS_UNAVAILABLE,
            self::STATUS_IN_USE,
            self::STATUS_MAINTENANCE,
            self::STATUS_RETIRED,
        ];
    }

    public static function getConditions(): array
    {
        return [
            self::CONDITION_NEW,
            self::CONDITION_GOOD,
            self::CONDITION_FAIR,
            self::CONDITION_POOR,
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function currentBooking()
    {
        return $this->hasOne(Booking::class)
            ->where('status', 'active')
            ->whereNull('returned_at');
    }

    public function history(): HasMany
    {
        return $this->hasMany(EquipmentHistory::class);
    }

    public function activeBookings(): HasMany
    {
        return $this->bookings()->where('status', 'active');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available' && !$this->hasActiveBooking();
    }

    public function hasActiveBooking(): bool
    {
        $now = Carbon::now();
        return $this->bookings()
            ->where('status', 'approved')
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->where('start_time', '<=', $now)
                      ->where('end_time', '>=', $now);
                });
            })
            ->exists();
    }

    public function hasUpcomingBooking(): bool
    {
        $now = Carbon::now();
        return $this->bookings()
            ->where('status', 'approved')
            ->where('start_time', '>', $now)
            ->exists();
    }

    public function getNextAvailableDateAttribute(): ?string
    {
        $latestBooking = $this->bookings()
            ->where('status', 'approved')
            ->where('end_time', '>', Carbon::now())
            ->orderBy('end_time', 'desc')
            ->first();

        return $latestBooking ? $latestBooking->end_time->format('M d, Y H:i') : null;
    }

    public function getCurrentBookingAttribute(): ?object
    {
        $now = Carbon::now();
        return $this->bookings()
            ->where('status', 'approved')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->hasActiveBooking()) {
            return 'Booked';
        }
        
        if ($this->hasUpcomingBooking()) {
            return 'Reserved';
        }

        return match($this->status) {
            'available' => 'Available',
            'maintenance' => 'Under Maintenance',
            'damaged' => 'Damaged',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->hasActiveBooking()) {
            return 'bg-yellow-100 text-yellow-800';
        }
        
        if ($this->hasUpcomingBooking()) {
            return 'bg-blue-100 text-blue-800';
        }

        return match($this->status) {
            'available' => 'bg-green-100 text-green-800',
            'maintenance' => 'bg-orange-100 text-orange-800',
            'damaged' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function updateStatus(string $status): void
    {
        $this->update(['status' => $status]);
        
        // Record the status change in history
        $this->history()->create([
            'user_id' => auth()->id(),
            'action' => 'status_change',
            'details' => "Status changed to {$status}",
        ]);
    }

    public function updateCondition($condition, $notes = null)
    {
        $oldCondition = $this->condition;
        $this->condition = $condition;
        $this->save();

        // Log the condition change
        DB::table('equipment_condition_logs')->insert([
            'equipment_id' => $this->id,
            'condition' => $condition,
            'notes' => $notes,
            'updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create notification for admin
        if ($oldCondition !== $condition) {
            Notification::create([
                'user_id' => 1, // Assuming admin has ID 1
                'title' => 'Equipment Condition Updated',
                'message' => "Equipment {$this->name} condition changed from {$oldCondition} to {$condition}",
                'type' => 'info'
            ]);
        }
    }

    public function conditionLogs()
    {
        return $this->hasMany(EquipmentConditionLog::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Get the maintenance logs for the equipment.
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * Get the scheduled maintenance for the equipment.
     */
    public function scheduledMaintenance(): HasMany
    {
        return $this->maintenanceLogs()
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now());
    }

    /**
     * Get the next maintenance date.
     */
    public function getNextMaintenanceDateAttribute()
    {
        return $this->scheduledMaintenance()
            ->orderBy('scheduled_date', 'asc')
            ->value('scheduled_date');
    }

    /**
     * Get the total maintenance cost.
     */
    public function getTotalMaintenanceCostAttribute()
    {
        return $this->maintenanceLogs()
            ->where('status', 'completed')
            ->sum('cost');
    }

    public function isAvailableForPeriod($startTime, $endTime, $excludeBookingId = null): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        $query = $this->bookings()
            ->where('status', 'approved')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    public function isAvailableForUser(?User $user = null): bool
    {
        if (!$this->is_active || $this->status !== 'available') {
            return false;
        }

        $activeBooking = $this->bookings()
            ->where('status', 'approved')
            ->where('end_date', '>', now())
            ->first();

        if (!$activeBooking) {
            return true;
        }

        // If there's an active booking, check if it belongs to the current user
        return $user && $activeBooking->user_id === $user->id;
    }

    public function current_booking()
    {
        return $this->hasOne(Booking::class)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->latest();
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    public function getUpcomingBookingsAttribute(): object
    {
        return $this->bookings()
            ->where('status', 'approved')
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->get();
    }
}
