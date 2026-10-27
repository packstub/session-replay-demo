<?php

use App\Logging\ReplayLaravelLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Payments\PaymentFailed;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Monolog\LogRecord;
use Opcodes\LogViewer\Logs\Log as LogViewerLog;

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

it('logs the failed payment without the card number or the machine path', function () {
    $this->actingAs($this->customer)->post(route('cart.add', Product::first()));

    try {
        $this->withoutExceptionHandling()->postJson('/checkout/pay', checkout(['card' => '4000 0000 0000 0119']));
    } catch (PaymentFailed $e) {
        $trace = $e->getTraceAsString();
    }

    expect($trace)->not->toContain('4000000000000119')->toContain('SensitiveParameterValue');

    $handler = Log::channel('single')->getLogger()->getHandlers()[0];
    $line = $handler->getFormatter()->format(new LogRecord(new DateTimeImmutable, 'test', Level::Error, 'x', ['exception' => $e]));

    expect($line)->toContain('app/Payments/PaymentGateway.php')->not->toContain(base_path().'/app');
});

it('shows a Replay column in Log Viewer with the link to the moment', function () {
    $moment = url('/session-replay/a22abc0d-f28d-4bc4-b336-d95259d95073?at=1793100868612');
    $line = '[2026-10-27 11:34:29] local.ERROR: The payment provider answered 502 {"userId":2} '.json_encode(['session_replay_moment' => $moment]);

    expect((new ReplayLaravelLog($line))->extra['session_replay'] ?? null)->toBe($moment)
        ->and(ReplayLaravelLog::$columns)->toContain(ReplayLaravelLog::COLUMN)
        ->and(LogViewerLog::$columns)->toContain(ReplayLaravelLog::COLUMN);

    $this->actingAs($this->support)->get('/log-viewer')->assertOk()->assertSee('replay-link', false);
});

it('signs in a demo account with one click, with Account Switcher\'s developer logins', function () {
    // Developer logins are for `local` only; the tests run as `testing`.
    config(['packstub-account-switcher.developer_logins.environments' => ['testing']]);

    $this->get('/login')->assertOk()->assertSee('olivia@acme.test')->assertSee('support@acme.test');

    $this->post(route('account-switcher.developer-login'), ['user' => $this->support->id])->assertRedirect(url('/session-replay'));
    $this->assertAuthenticatedAs($this->support);
});

it('offers no developer logins outside local', function () {
    $this->get('/login')->assertOk()->assertDontSee(route('account-switcher.developer-login'));
    $this->post(route('account-switcher.developer-login'), ['user' => $this->support->id])->assertNotFound();
    $this->assertGuest();
});
