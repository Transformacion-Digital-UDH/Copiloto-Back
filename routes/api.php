<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', [DemoController::class, 'login']); // inicio de sesión

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); //obtener el usuario


Route::post('/logout', [DemoController::class, 'logout']); //cierre de sesión