<?php

namespace App\Payments;

use Illuminate\Support\Str;

/**
 * A stand-in for a real payment provider, with the test cards people know from Stripe:
 * 4242 4242 4242 4242 succeeds, 4000 0000 0000 0119 fails with a processing error.
 */
class PaymentGateway
{
    public function charge(int $cents, string $card): string
    {
        usleep(900_000); // a round trip to the provider

        if ($card === '4000000000000119') {
            throw new PaymentFailed("The payment provider answered 502 for a charge of {$cents} cents (processing_error).");
        }

        return 'ch_'.Str::lower(Str::random(16));
    }
}
