<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'user_type',
        'is_active',
        'hourly_rate',
        'monthly_rental',
        'rental_start_date',
        'business_description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'monthly_rental' => 'decimal:2',
            'rental_start_date' => 'date',
        ];
    }

    // Relationships
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function tenantRentals(): HasMany
    {
        return $this->hasMany(TenantRental::class, 'tenant_id');
    }

    public function staffAttendance(): HasMany
    {
        return $this->hasMany(StaffAttendance::class, 'staff_id');
    }

    public function createdExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUserType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    public function scopeTenants($query)
    {
        return $query->where('user_type', 'tenant');
    }

    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    public function scopeStaff($query)
    {
        return $query->where('user_type', 'staff');
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isTenant()
    {
        return $this->user_type === 'tenant';
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    public function isStaff()
    {
        return $this->user_type === 'staff';
    }

    public function isGuest()
    {
        return $this->user_type === 'guest';
    }

    public function canManageMenuItems()
    {
        return in_array($this->user_type, ['admin', 'tenant']);
    }

    public function canManageOrders()
    {
        return in_array($this->user_type, ['admin', 'tenant', 'staff']);
    }

    public function ownsMenuItem(MenuItem $menuItem)
    {
        return $this->id === $menuItem->user_id || $this->isAdmin();
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?: 'Guest User';
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }
}
