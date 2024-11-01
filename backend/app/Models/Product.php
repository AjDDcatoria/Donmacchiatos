<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static create(array $array)
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
