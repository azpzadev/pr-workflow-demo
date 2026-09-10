<?php

namespace App\Services;

use InvalidArgumentException;

class DiscountService
{
    private const MAX_PERCENT = 100;

    /**
     * ဈေးနှုန်း (ကျပ်) ပေါ်မှာ ရာခိုင်နှုန်းလျှော့ပြီး ပေးရမယ့်ငွေ ပြန်ပေးသည်။
     */
    public function finalPrice(int $priceMmk, int $percent): int
    {
        if ($priceMmk < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        $percent = max(0, min(self::MAX_PERCENT, $percent));

        return $priceMmk - (int) round($priceMmk * $percent / 100);
    }
}
