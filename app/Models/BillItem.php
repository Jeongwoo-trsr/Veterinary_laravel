<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}