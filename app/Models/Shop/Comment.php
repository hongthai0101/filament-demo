<?php

namespace App\Models\Shop;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'commentable_id',
        'commentable_type',
        'title',
        'content',
        'is_visible',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
