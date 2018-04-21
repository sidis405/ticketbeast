<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function chargesWithAValidPaymentTokenAreSuccessful()
    {
        $gateway = new FakePaymentGateway;
        $gateway->charge(2500, $gateway->getValidTestToken());

        $this->assertEquals(2500, $gateway->totalCharges());
    }
}
