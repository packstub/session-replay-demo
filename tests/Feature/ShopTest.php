<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Payments\PaymentFailed;

beforeEach(function () {
    $this->seed();
    $this->customer = User::where('email', 'olivia@acme.test')->first();
    $this->support = User::where('email', 'support@acme.test')->first();
});

function checkout(array $overrides = []): array
{
    return [
        'name' => 'Olivia Bennett',
        'address' => '42 Elm Road, Bristol',
        'card' => '4242 4242 4242 4242',
        'expiry' => '09 / 28',
        'cvc' => '512',
        ...$overrides,
    ];
}

it('records customers and not support', function () {
    $this->actingAs($this->customer)->get('/shop')->assertOk()->assertSee('session-replay/scripts', false);
    $this->actingAs($this->support)->get('/shop')->assertOk()->assertDontSee('session-replay/scripts', false);
});

it('lets support watch and keeps customers out', function () {
    $this->actingAs($this->support)->get('/session-replay')->assertOk();
    $this->actingAs($this->customer)->get('/session-replay')->assertForbidden();
});

it('takes a payment with the card that works', function () {
    $this->actingAs($this->customer)->post(route('cart.add', Product::first()));

    $this->postJson('/checkout/pay', checkout())->assertOk()->assertJsonStructure(['redirect']);

    expect(Order::sole())->card_last4->toBe('4242')->user_id->toBe($this->customer->id);
});

it('fails at the provider with the card that fails', function () {
    $this->actingAs($this->customer)->post(route('cart.add', Product::first()));

    $this->withoutExceptionHandling();

    expect(fn () => $this->postJson('/checkout/pay', checkout(['card' => '4000 0000 0000 0119'])))->toThrow(PaymentFailed::class);
    expect(Order::count())->toBe(0);
});

it('answers a validation error as JSON', function () {
    $this->actingAs($this->customer)->post(route('cart.add', Product::first()));

    $this->postJson('/checkout/pay', checkout(['cvc' => '']))->assertUnprocessable()->assertJsonValidationErrors('cvc');
});
