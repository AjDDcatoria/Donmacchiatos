<?php

namespace App\Constants;

enum OrderStatus
{
    const ACCEPTED = 'accepted';
    const DECLINED = 'declined';
    const PENDING = 'pending';
    const CANCELED = 'canceled';
}
