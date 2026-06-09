<?php

use App\Http\Controllers\Webhook\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Webhooks não necessitam de autenticação
Route::post('/webhook/stripe', StripeWebhookController::class)->name('webhook.stripe');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
