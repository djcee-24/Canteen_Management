<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('address');
            $table->enum('user_type', ['admin', 'tenant', 'customer', 'staff', 'guest'])->default('customer')->after('avatar');
            $table->boolean('is_active')->default(true)->after('user_type');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('is_active'); // For staff
            $table->decimal('monthly_rental', 10, 2)->nullable()->after('hourly_rate'); // For tenants
            $table->date('rental_start_date')->nullable()->after('monthly_rental');
            $table->text('business_description')->nullable()->after('rental_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'avatar',
                'user_type',
                'is_active',
                'hourly_rate',
                'monthly_rental',
                'rental_start_date',
                'business_description'
            ]);
        });
    }
};