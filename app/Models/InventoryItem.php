<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'current_stock',
        'minimum_stock_level',
        'unit',
        'selling_price',
        'expiry_date',
        'batch_number',
        'supplier_name',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Helper Methods
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock_level;
    }

   public function isExpired(): bool
{
    if (!$this->expiry_date) {
        return false;
    }
    return $this->expiry_date < now()->startOfDay();
}

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date <= now()->addDays($days);
    }

    // Stock Management
    public function addStock(int $quantity, string $reference = null, string $notes = null): void
    {
        $previousStock = $this->current_stock;
        $this->current_stock += $quantity;
        $this->save();

        $this->transactions()->create([
            'type' => 'in',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'reference' => $reference,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);
    }

    public function reduceStock(int $quantity, string $reference = null, string $notes = null): void
    {
        $previousStock = $this->current_stock;
        $this->current_stock = max(0, $this->current_stock - $quantity);
        $this->save();

        $this->transactions()->create([
            'type' => 'out',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'reference' => $reference,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock_level');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}