<?php

namespace App\Services;

use InvalidArgumentException;

class DiscountService
{
    private const MAX_PERCENT = 100;

    /** coupon code => လျှော့ရာခိုင်နှုန်း */
    private const COUPONS = [
        'DEVTALK' => 15,
        'THINGYAN' => 30,
        'VIP' => 50,
    ];

    public function finalPrice(int $priceMmk, int $percent = 0, ?string $coupon = null): int
    {
        if ($priceMmk < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        $percent += $this->couponPercent($coupon);

        $percent = max(0, min(self::MAX_PERCENT, $percent));

        return $priceMmk - (int) round($priceMmk * $percent / 100);
    }

    private function couponPercent(?string $coupon): int
    {
        if ($coupon === null || trim($coupon) === '') {
            return 0;
        }

        $code = strtoupper(trim($coupon));

        if (! array_key_exists($code, self::COUPONS)) {
            throw new InvalidArgumentException("Unknown coupon code: {$code}");
        }

        return self::COUPONS[$code];
    }
}
