<?php

namespace App\Services\Payment;

class PaymentResult
{
    public bool $success;
    public bool $paid;
    public ?string $reference;
    public ?string $message;

    public function __construct(bool $success, bool $paid, ?string $reference = null, ?string $message = null)
    {
        $this->success = $success;
        $this->paid = $paid;
        $this->reference = $reference;
        $this->message = $message;
    }

    public static function success(string $reference, ?string $message = null): self
    {
        return new self(success: true, paid: true, reference: $reference, message: $message);
    }

    public static function pending(?string $reference = null, ?string $message = null): self
    {
        return new self(success: true, paid: false, reference: $reference, message: $message);
    }

    public static function fail(?string $message = null): self
    {
        return new self(success: false, paid: false, message: $message);
    }
}
