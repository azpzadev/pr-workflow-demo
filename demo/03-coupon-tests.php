<?php
// tests/Unit/DiscountServiceTest.php ရဲ့ အောက်ဆုံးမှာ ဒါတွေ ထပ်ဖြည့်ပါ

it('applies a coupon discount', function () {
    expect($this->service->finalPrice(10_000, 0, 'DEVTALK'))->toBe(8_500);
});

it('accepts a coupon in any casing or with spaces', function () {
    expect($this->service->finalPrice(10_000, 0, '  devtalk '))->toBe(8_500);
});

it('never lets a stacked discount go past 100 percent', function () {
    expect($this->service->finalPrice(10_000, 80, 'THINGYAN'))->toBe(0);
});

it('rejects an unknown coupon instead of ignoring it', function () {
    expect(fn () => $this->service->finalPrice(10_000, 0, 'FREE-MONEY'))
        ->toThrow(InvalidArgumentException::class);
});
