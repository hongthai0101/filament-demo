<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'provider',
        'method',
        'amount',
        'currency'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
