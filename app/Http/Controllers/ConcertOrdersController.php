<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Billing\PaymentFailedException;

class ConcertOrdersController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Concert $concert)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'ticketQuantity' => 'required|integer|min:1',
                'paymentToken' => 'required',
            ]
        );

        try {
            $this->paymentGateway->charge(
                $request->get('ticketQuantity') * $concert->ticket_price,
                $request->get('paymentToken')
            );

            $concert->orderTickets($request->get('email'), $request->get('ticketQuantity'));

            return response()->json([], 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function show(Concert $concert)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function edit(Concert $concert)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Concert $concert)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function destroy(Concert $concert)
    {
        //
    }
}
