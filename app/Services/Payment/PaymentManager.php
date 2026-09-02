<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentManager
{
    private array $providers = [];

    public function provider(?string $driver = null): PaymentProvider
    {
        $driver = $driver ?: config('payment.driver', 'mock');
        $driver = (string) $driver;

        if (isset($this->providers[$driver])) {
            return $this->providers[$driver];
        }

        $provider = match ($driver) {
            'mock' => new MockPaymentProvider(),
            'midtrans' => new MidtransPaymentProvider(),
            default => throw new InvalidArgumentException("Payment provider [{$driver}] tidak didukung."),
        };

        return $this->providers[$driver] = $provider;
    }
}
