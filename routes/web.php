<?php

use App\Models\Client;
use Illuminate\Support\Facades\Route;

/*
$inactiveClients = Client::inactive()->get();

*/

Route::get('/', function () {

    $client = Client::query()
        ->where('id', 14)
        ->first();

    return response()->json([
        'active' => $client->isAdult(),

    ]);
});

require __DIR__.'/auth.php';
