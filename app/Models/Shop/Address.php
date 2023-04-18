<?php

namespace App\Models\Shop;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'addressable',
        'country',
        'street',
        'city',
        'state',
        'zip',
        'addressable_type',
        'addressable_id',
    ];

    public function customers(): MorphToMany
    {
        return $this->morphedByMany(Customer::class, 'addressable');
    }

    public function brands(): MorphToMany
    {
        return $this->morphedByMany(Brand::class, 'addressable');
    }
}
