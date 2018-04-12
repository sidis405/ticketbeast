<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function userCanViewAPublishedConcertListing()
    {
        $concert = create(
            Concert::class,
            [
                'title' => 'Daft Punk Live',
                'subtitle' => 'Funkorama',
                'date' => Carbon::parse('July 16th 2018 8:00pm'),
                'ticket_price' => 3250,
                'venue' => 'Stadio Olimpico',
                'venue_address' => 'Viale dei Gladiatori',
                'state' => 'RM',
                'city' => 'Roma',
                'zip' => '00135',
                'additional_information' => 'For info call: (555) 555-3456.',
            ],
            'published'
        );

        // $response = $this->get('/concerts/' . $concert->id);
        $response = $this->get(route('concerts.show', $concert));

        $response->assertSee('Daft Punk Live');
        $response->assertSee('Funkorama');
        $response->assertSee('July 16, 2018');
        $response->assertSee('8:00pm');
        $response->assertSee('€32,50');
        $response->assertSee('Stadio Olimpico');
        $response->assertSee('Viale dei Gladiatori');
        $response->assertSee('00135, Roma, RM');
        $response->assertSee('For info call: (555) 555-3456.');
    }

    /** @test */
    public function userCannotViewUnpublishedConcertListings()
    {
        $this->withExceptionHandling();

        $concert = create(Concert::class, [], 'unpublished');

        $response = $this->get(route('concerts.show', $concert));

        $response->assertStatus(404);
    }
}
