<?php

namespace App\Services\Payment;

use App\Models\Subscription;

/**
 * Provider pembayaran simulasi (mode development/demo).
 * Tidak melakukan pembayaran sungguhan; menghasilkan instruksi membayar
 * dan dapat dikonfirmasi otomatis untuk menguji alur end-to-end.
 */
class MockPaymentProvider implements PaymentProvider
{
    public function name(): string
    {
        return 'mock';
    }

    public function createPayment(Subscription $subscription): array
    {
        $reference = 'MOCK-'.strtoupper(bin2hex(random_bytes(4)));
        $subscription->update(['payment_method' => 'Transfer Bank (Simulasi)', 'reference' => $reference, 'provider' => $this->name()]);

        return [
            'redirect_url' => route('subscription.pay', $subscription),
            'instruction' => 'Transfer simulasi sebesar '.number_format($subscription->amount, 0, ',', '.').' lalu konfirmasi pembayaran.',
        ];
    }

    public function handleCallback(array $payload, Subscription $subscription): PaymentResult
    {
        // Dalam mode mock, begitu user mengunjungi halaman payment -> dianggap dibayar.
        return PaymentResult::success(reference: $subscription->reference, message: 'Pembayaran mock berhasil dikonfirmasi.');
    }
}
