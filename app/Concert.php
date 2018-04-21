<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];
    protected $casts = [
        'date' => 'datetime'
    ];

    protected $appends = [
        'formatted_date',
        'formatted_start_time',
        'ticket_price_in_euros',
    ];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F d, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInEurosAttribute()
    {
        return '€' . number_format($this->ticket_price /100, 2, ',', '.');
    }

    public function scopePublished($builder)
    {
        return $builder->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderTickets(string $email, int $ticketQuantity)
    {
        $order = $this->orders()->create(compact('email'));


        foreach (range(1, $ticketQuantity) as $i) {
            $order->tickets()->create([]);
        }

        return $order->fresh();
    }
}
