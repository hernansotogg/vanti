<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClienteController;


// Rutas vanti
Route::get('/', [ClienteController::class, 'index'])->name('index');
Route::post('/saveUser', [ClienteController::class, 'saveUser']);

Route::get('/detail', [ClienteController::class, 'detail'])->name('proceso.pago')->middleware('phone.session');
Route::post('/pago', [ClienteController::class, 'pago'])->middleware('phone.session');

// Rutas de respuesta de MercadoPago
Route::get('/pago/success', [ClienteController::class, 'pagoSuccess'])->name('pago.success');
Route::get('/pago/failure', [ClienteController::class, 'pagoFailure'])->name('pago.failure');
Route::get('/pago/pending', [ClienteController::class, 'pagoPending'])->name('pago.pending');
Route::post('/pago/webhook', [ClienteController::class, 'pagoWebhook'])->name('pago.webhook');
