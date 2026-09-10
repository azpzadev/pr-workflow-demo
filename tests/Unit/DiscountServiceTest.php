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
