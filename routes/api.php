<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamplesOpenAI\ChatModelController;
use App\Http\Controllers\ExamplesOpenAI\BankSlipAnalizerController;
use App\Http\Controllers\ExamplesOpenAI\ProofPaymentAnalizerController;

Route::get('/chat-model', [ ChatModelController::class, 'index']);
Route::post('/proof-payment-analizer', [ ProofPaymentAnalizerController::class, 'index']);
Route::post('/bank-slip-analizer', [ BankSlipAnalizerController::class, 'index']);
