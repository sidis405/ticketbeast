<div>{{ $concert->title}}</div>
<div>{{ $concert->subtitle}}</div>
<div>{{ $concert->date->format('F d, Y') }}</div>
<div>{{ $concert->date->format('g:ia') }}</div>
<div>{{ number_format($concert->ticket_price /100, 2, '.', ',') }}</div>
<div>{{ $concert->venue}}</div>
<div>{{ $concert->venue_address}}</div>
<div>{{ $concert->zip}}, {{ $concert->city}}, {{ $concert->state}}</div>
<div>{{ $concert->additional_information}}</div>
