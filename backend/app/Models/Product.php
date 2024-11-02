<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method create(array $array)
 * @method updateOrCreate(array $array, array $array1)
 */
class Product extends Model
{
    use HasUuids,HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image'
    ];
}