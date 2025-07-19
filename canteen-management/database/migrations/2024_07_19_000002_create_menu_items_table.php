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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->integer('preparation_time')->default(15); // minutes
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('allergens')->nullable(); // Store allergen info as JSON
            $table->json('dietary_info')->nullable(); // vegan, vegetarian, etc.
            $table->integer('calories')->nullable();
            $table->text('ingredients')->nullable();
            $table->foreignId('menu_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Owner of the menu item
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'is_available']);
            $table->index(['menu_category_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};