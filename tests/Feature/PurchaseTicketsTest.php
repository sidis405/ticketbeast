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

    /** @test */
    public function customerCanPurchaseConcertTickets()
    {
        //arrange
        //create concert
        $ticket_price = 3250;
        $ticketQuantity = 3;
        $email = 'sherlock@221b.co.uk';
        $concert = create(Concert::class, ['ticket_price' => $ticket_price]);

        //act
        //purchase concert tickets
        $response = $this->orderTickets($concert, [
            'email' => $email,
            'ticketQuantity' => $ticketQuantity,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken()
        ]);

        //assert
        $response->assertStatus(201);
        //make sure the customer was charged the correct amount
        $this->assertEquals($ticketQuantity * $ticket_price, $this->fakePaymentGateway->totalCharges());
        //make sure and order exists
        $order = $concert->orders()->where('email', $email)->first();
        $this->assertNotNull($order);
        $this->assertEquals($ticketQuantity, $order->tickets()->count());
    }

    /** @test */
    public function emailIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class);

        $response = $this->orderTickets($concert, [
            'ticketQuantity' => 3,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken()
        ]);


        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json()['errors']);
    }

    /** @test */
    public function validEmailIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class);

        $response = $this->orderTickets($concert, [
            'email' => 'what-is-this',
            'ticketQuantity' => 3,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken()
        ]);


        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json()['errors']);
    }

    /** @test */
    public function ticketQuantityIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class);

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken()
        ]);


        $response->assertStatus(422);
        $this->assertArrayHasKey('ticketQuantity', $response->json()['errors']);
    }

    /** @test */
    public function validTicketQuantityIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class);

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'ticketQuantity' => 'not-a-quantity',
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken()
        ]);


        $response->assertStatus(422);
        $this->assertArrayHasKey('ticketQuantity', $response->json()['errors']);
    }

    /** @test */
    public function aQuantityOfAtLeastIsRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class);

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'ticketQuantity' => 0,
            'paymentToken' => $this->fakePaymentGateway->getValidTestToken()
        ]);


        $response->assertStatus(422);
        $this->assertArrayHasKey('ticketQuantity', $response->json()['errors']);
    }

    /** @test */
    public function aTokenRequiredToPurchaseTickets()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class);

        $response = $this->orderTickets($concert, [
            'email' => 'sid@trandafili.com',
            'ticketQuantity' => 3
        ]);


        $response->assertStatus(422);
        $this->assertArrayHasKey('paymentToken', $response->json()['errors']);
    }
}
