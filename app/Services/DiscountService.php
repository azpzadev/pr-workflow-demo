<?php

namespace App\Services;

use InvalidArgumentException;

class DiscountService
{
    /** coupon code => လျှော့ရာခိုင်နှုန်း */
    private const COUPONS = [
        'DEVTALK' => 15,
        'THINGYAN' => 30,
        'VIP' => 150,
    ];

    public function finalPrice(int $priceMmk, int $percent = 0, ?string $coupon = null): int
    {
        if ($priceMmk < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        if ($coupon !== null) {
            $percent = $percent + (self::COUPONS[$coupon] ?? 0);
        }

        return $priceMmk - (int) round($priceMmk * $percent / 100);
    }
}
