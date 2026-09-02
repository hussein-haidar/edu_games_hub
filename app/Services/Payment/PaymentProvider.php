<?php

namespace App\Services\Payment;

use App\Models\Subscription;

interface PaymentProvider
{
    /**
     * Nama unik provider (mock, midtrans, dst).
     */
    public function name(): string;

    /**
     * Buat pembayaran untuk sebuah subscription.
     * Mengembalikan array berisi petunjuk lanjutan, mis. ['redirect_url' => ...] atau ['instruction' => ...].
     *
     * @return array<string, mixed>
     */
    public function createPayment(Subscription $subscription): array;

    /**
     * Verifikasi & proses hasil pembayaran (dari redirect/notifikasi).
     *
     * @param  array<string, mixed>  $payload
     * @return PaymentResult
     */
    public function handleCallback(array $payload, Subscription $subscription): PaymentResult;
}
