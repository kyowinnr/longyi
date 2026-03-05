<?php

use App\Http\Controllers\Api\RwsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/rws/ledger', [RwsController::class, 'ledger']);
