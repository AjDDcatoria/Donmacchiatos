<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, $id)
 * @method static create(array $array)
 * @property mixed $user_id
 */
class Order extends Model
{
    use HasUuids;
    protected $fillable = ['user_id','payment' ,'grand_total', 'message'];

    public function items():HasMany
    {
        return $this->hasMany(OrderItem::class)->with('product');
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
