<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'category',
        'expense_date',
        'receipt_image',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', Carbon::now()->month)
                    ->whereYear('expense_date', Carbon::now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('expense_date', Carbon::now()->year);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('expense_date', Carbon::today());
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function getCategoryColorAttribute()
    {
        return match($this->category) {
            'utilities' => 'warning',
            'supplies' => 'info',
            'maintenance' => 'danger',
            'salaries' => 'success',
            'marketing' => 'primary',
            'other' => 'secondary',
            default => 'secondary'
        };
    }

    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'utilities' => 'Utilities',
            'supplies' => 'Supplies',
            'maintenance' => 'Maintenance',
            'salaries' => 'Salaries',
            'marketing' => 'Marketing',
            'other' => 'Other',
            default => ucfirst($this->category)
        };
    }
}