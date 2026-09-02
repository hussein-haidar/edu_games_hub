<?php

namespace App\Services\Payment;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Provider Midtrans (Snap).
 *
 * CATATAN: Untuk produksi, isi MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY di .env,
 * lalu aktifkan via config/services.php (driver 'midtrans').
 * Implementasi menggunakan HTTP ke Snap API.
 */
class MidtransPaymentProvider implements PaymentProvider
{
    private string $serverKey;
    private bool $isProduction;

    public function name(): string
    {
        return 'midtrans';
    }

    public function __construct()
    {
        $this->serverKey = config('payment.midtrans.server_key', '');
        $this->isProduction = (bool) config('payment.midtrans.is_production', false);
    }

    private function baseUrl(): string
    {
        return $this->isProduction ? 'https://app.midtrans.com/snap/v1' : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    public function createPayment(Subscription $subscription): array
    {
        if (blank($this->serverKey)) {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $item = $subscription->plan?->nama ?? 'Langganan';
        $customer = $subscription->user;

        $payload = [
            'transaction_details' => [
                'order_id' => $subscription->order_id,
                'gross_amount' => (int) round($subscription->amount),
            ],
            'item_details' => [
                ['id' => 'sub'.$subscription->id, 'price' => (int) round($subscription->amount), 'quantity' => 1, 'name' => $item],
            ],
            'customer_details' => [
                'first_name' => $customer->name,
                'email' => $customer->email,
            ],
            'callbacks' => [
                'finish' => route('subscription.callback', $subscription),
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->post($this->baseUrl().'/transactions', $payload);

        if (! $response->successful() || ! isset($response['redirect_url'])) {
            Log::error('Midtrans create payment failed', ['body' => $response->body()]);
            throw new \RuntimeException('Gagal membuat pembayaran Midtrans.');
        }

        $subscription->update([
            'payment_method' => 'Midtrans',
            'reference' => $response['token'] ?? null,
            'provider' => $this->name(),
        ]);

        return ['redirect_url' => $response['redirect_url']];
    }

    public function handleCallback(array $payload, Subscription $subscription): PaymentResult
    {
        $statusCode = $payload['status_code'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $orderId = $payload['order_id'] ?? null;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            return PaymentResult::success(reference: $subscription->reference, message: 'Pembayaran diterima.');
        }

        if ($transactionStatus === 'pending') {
            return PaymentResult::pending(reference: $subscription->reference);
        }

        return PaymentResult::fail('Pembayaran gagal (status: '.$transactionStatus.').');
    }
}
