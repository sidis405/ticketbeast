<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canGetFormattedDate()
    {
        $concert = make(Concert::class, ['date' => Carbon::parse('2018-07-16 8:00pm')]);
        $date = $concert->formatted_date;
        $this->assertEquals('July 16, 2018', $date);
    }

    /** @test */
    public function canGetFormattedStartTime()
    {
        $concert = make(Concert::class, ['date' => Carbon::parse('2018-07-16 8:00pm')]);
        $time = $concert->formatted_start_time;
        $this->assertEquals('8:00pm', $time);
    }

    /** @test */
    public function canGetTicketPriceInEuros()
    {
        $concert = make(Concert::class, ['ticket_price' => 4500]);
        $price = $concert->ticket_price_in_euros;
        $this->assertEquals('€45,00', $price);
    }

    /** @test */
    public function concertsWithAPublishedAtDateArePublished()
    {
        $publishedConcertA = create(Concert::class, [], 'published');
        $publishedConcertB = create(Concert::class, [], 'published');
        $unpublishedConcert = create(Concert::class, [], 'unpublished');

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertTrue(!$publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    public function canOrderconcertTickets()
    {
        $concert = create(Concert::class);

        $order = $concert->orderTickets('sid@trandafili.com', 3);

        $this->assertEquals('sid@trandafili.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }
}
