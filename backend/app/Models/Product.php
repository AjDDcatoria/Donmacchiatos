<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method create(array $array)
 * @method updateOrCreate(array $array, array $array1)
 * @method static whereIn(string $string, array $productsIds)
 */
class Product extends Model
{
    use HasUuids,HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image'
    ];

    public function orderItem():HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
