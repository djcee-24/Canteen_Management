<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TenantRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'monthly_rental_fee',
        'billing_start_date',
        'billing_end_date',
        'status',
        'paid_amount',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'monthly_rental_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'billing_start_date' => 'date',
        'billing_end_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('billing_start_date', Carbon::now()->month)
                    ->whereYear('billing_start_date', Carbon::now()->year);
    }

    public function getFormattedFeeAttribute()
    {
        return '$' . number_format($this->monthly_rental_fee, 2);
    }

    public function getFormattedPaidAmountAttribute()
    {
        return '$' . number_format($this->paid_amount, 2);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->monthly_rental_fee - $this->paid_amount;
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return '$' . number_format($this->remaining_amount, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    public function isOverdue()
    {
        return Carbon::now()->isAfter($this->billing_end_date) && $this->status !== 'paid';
    }

    public function markAsPaid($amount = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_amount' => $amount ?? $this->monthly_rental_fee,
            'paid_at' => now(),
        ]);
    }
}