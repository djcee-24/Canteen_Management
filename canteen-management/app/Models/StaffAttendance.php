<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'hours_worked',
        'hourly_rate',
        'daily_pay',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'daily_pay' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($attendance) {
            $attendance->calculateDailyPay();
        });

        static::updating(function ($attendance) {
            if ($attendance->isDirty(['hours_worked', 'hourly_rate'])) {
                $attendance->calculateDailyPay();
            }
        });
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('attendance_date', Carbon::now()->month)
                    ->whereYear('attendance_date', Carbon::now()->year);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('attendance_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', Carbon::today());
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function calculateHoursWorked()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_time);
            
            $hours = $checkOut->diffInMinutes($checkIn) / 60;
            $this->hours_worked = round($hours, 2);
        }
    }

    public function calculateDailyPay()
    {
        $this->daily_pay = $this->hours_worked * $this->hourly_rate;
    }

    public function getFormattedDailyPayAttribute()
    {
        return '₱' . number_format($this->daily_pay, 2);
    }

    public function getFormattedHourlyRateAttribute()
    {
        return '₱' . number_format($this->hourly_rate, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'half_day' => 'info',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'half_day' => 'Half Day',
            default => ucfirst($this->status)
        };
    }

    public function checkOut($time = null)
    {
        $this->update([
            'check_out_time' => $time ?? now()->format('H:i'),
        ]);
        
        $this->calculateHoursWorked();
        $this->calculateDailyPay();
        $this->save();
    }
}