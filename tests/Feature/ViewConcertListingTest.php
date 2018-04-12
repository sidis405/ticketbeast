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
    public function userCanViewAConcertListing()
    {
        //arrange
        // Create contert
        // Direct Model Access - Testing RSPEC book
        $concert = Concert::create(
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
                'additional_information' => 'For info call: (555) 555-3456.'
            ]
        );

        //act
        // View Listing
        $response = $this->get('/concerts/' . $concert->id);

        // dd($response->json());

        // //assert
        // // See deetails
        $response->assertSee('Daft Punk Live');
        $response->assertSee('Funkorama');
        $response->assertSee('July 16, 2018');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('Stadio Olimpico');
        $response->assertSee('Viale dei Gladiatori');
        $response->assertSee('00135, Roma, RM');
        $response->assertSee('For info call: (555) 555-3456.');
    }
}
