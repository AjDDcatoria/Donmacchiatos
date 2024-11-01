<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static create(array $array)
 * @method static where(string $string, mixed $verification_id)
 */
class Verification extends Model
{
    use HasUuids;

    public mixed $code;
    /**
     * @var Carbon|mixed
     */

    public mixed $verified;
    public mixed $email;
    protected $fillable = [
        'email',
        'code',
        'verified',
        'expired_at',
    ];

}
