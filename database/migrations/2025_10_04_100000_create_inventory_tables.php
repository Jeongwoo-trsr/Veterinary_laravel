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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['Medicine', 'Consumables', 'Equipment', 'Pet Food']);
            $table->text('description')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock_level')->default(10);
            $table->string('unit')->default('pieces'); // pieces, bottles, boxes, kg, etc.
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('supplier_name')->nullable();
            $table->timestamps();
        });

        // Optional: Track stock movements/transactions
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment']); // stock in, stock out, manual adjustment
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->string('reference')->nullable(); // appointment_id, purchase_order, etc.
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who made the transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
    }
};