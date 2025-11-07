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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('pet_owners')->onDelete('cascade');
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->integer('age');
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('color')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('microchip_id')->nullable()->unique();
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};