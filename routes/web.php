<?php

Route::get('concerts/{id}', 'ConcertsController@show')->name('concerts.show');
Route::resource('concerts.orders', 'ConcertOrdersController')->only('store');
