<?php

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected $fakePaymentGateway;

    public function setUp()
    {
        parent::setUp();

        $this->fakePaymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->fakePaymentGateway);
    }

    private function orderTickets($concert, $params)
    {
        return $this->postJson(route('concerts.orders.store', $concert), $params);
    }

    private function assertValidationErrorFor($response, $field)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($field, $response->json()['errors']);
    }

    /** @test */
    public function customerCanPurchaseConcertPublishedTickets()
    {
        $ticket_price = 3250;
        $ticketQuantity = 3;
        $email = 'sherlock@221b.co.uk';
        $concert = create(Concert::class, ['ticket_price' => $ticket_price], 'published');

        $response = $this->orderTickets($concert, [
            'email' => $email,
            'ticketQuantity' => $ticketQuantity,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(201);
        $this->assertEquals($ticketQuantity * $ticket_price, $this->fakePaymentGateway->totalCharges());
        $order = $concert->orders()->where('email', $email)->first();
        $this->assertNotNull($order);
        $this->assertEquals($ticketQuantity, $order->tickets()->count());
    }

    /** @test */
    public function emailIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'published');

        $response = $this->orderTickets($concert, [
            'ticketQuantity' => 3,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationErrorFor($response, 'email');
    }

    /** @test */
    public function validEmailIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'published');

        $response = $this->orderTickets($concert, [
            'email' => 'what-is-this',
            'ticketQuantity' => 3,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationErrorFor($response, 'email');
    }

    /** @test */
    public function ticketQuantityIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'published');

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationErrorFor($response, 'ticketQuantity');
    }

    /** @test */
    public function validTicketQuantityIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'published');

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'ticketQuantity' => 'not-a-quantity',
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationErrorFor($response, 'ticketQuantity');
    }

    /** @test */
    public function aQuantityOfAtLeastIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'published');

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'ticketQuantity' => 0,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationErrorFor($response, 'ticketQuantity');
    }

    /** @test */
    public function aTokenRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'published');

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'ticketQuantity' => 3,
        ]);

        $this->assertValidationErrorFor($response, 'paymentToken');
    }

    /** @test */
    public function anOrderIsNotCreatedIfPaymentFails()
    {
        $concert = create(Concert::class, [], 'published');
        $this->withExceptionHandling();

        $response = $this->orderTickets($concert, [
            'email' => 'sherlock@221b.co.uk',
            'ticketQuantity' => 3,
            'paymentToken' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->where('email', 'sherlock@221b.co.uk')->first();
        $this->assertNull($order);
    }

    /** @test */
    public function cannotPurchaseTicketsToAnUnpublishedConcert()
    {
        $this->withExceptionHandling();
        $concert = create(Concert::class, [], 'unpublished');

        $response = $this->orderTickets($concert, [
            'email' => 'sherlock@221b.co.uk',
            'ticketQuantity' => 3,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
    }
}
