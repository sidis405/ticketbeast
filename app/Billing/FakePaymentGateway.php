<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }


    public function getValidTestToken() : string
    {
        return 'valid-token';
    }

    public function charge(int $amount, string $token) : void
    {
        $this->charges[] = $amount;
    }

    public function totalCharges() : int
    {
        return $this->charges->sum();
    }
}
