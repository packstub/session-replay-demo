<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Models\Order;
use App\Payments\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Cart $cart): View|RedirectResponse
    {
        if ($cart->count() === 0) {
            return redirect()->route('shop');
        }

        return view('shop.checkout', ['lines' => $cart->lines(), 'total' => $cart->total()]);
    }

    public function pay(Request $request, Cart $cart, PaymentGateway $gateway): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            'card' => ['required', 'string'],
            'expiry' => ['required', 'regex:/^\d{2}\s*\/\s*\d{2}$/'],
            'cvc' => ['required', 'digits_between:3,4'],
        ]);

        $card = preg_replace('/\D/', '', $data['card']);
        abort_if($cart->count() === 0, 422, 'Your cart is empty.');

        $total = $cart->total();
        $charge = $gateway->charge($total, $card);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'items' => $cart->lines()->map(fn (array $line) => [
                'name' => $line['product']->name,
                'quantity' => $line['quantity'],
                'total' => $line['total'],
            ])->values()->all(),
            'total' => $total,
            'card_last4' => substr($card, -4),
            'charge_id' => $charge,
            'address' => $data['name']."\n".$data['address'],
        ]);

        $cart->clear();

        return response()->json(['redirect' => route('orders.show', $order)]);
    }
}
