<?php

use App\Services\DiscountService;

beforeEach(function () {
    $this->service = new DiscountService;
});

it('applies a normal percentage discount', function () {
    expect($this->service->finalPrice(10_000, 10))->toBe(9_000);
});

it('returns the full price when there is no discount', function () {
    expect($this->service->finalPrice(10_000, 0))->toBe(10_000);
});

it('clamps a percentage above 100', function () {
    expect($this->service->finalPrice(10_000, 250))->toBe(0);
});

it('clamps a negative percentage', function () {
    expect($this->service->finalPrice(10_000, -50))->toBe(10_000);
});

it('rejects a negative price', function () {
    expect(fn () => $this->service->finalPrice(-1, 10))
        ->toThrow(InvalidArgumentException::class);
});
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
