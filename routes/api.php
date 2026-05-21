<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TranslationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| Public API Routes (Accessible by Guests)
|--------------------------------------------------------------------------
*/
Route::get('/notes', [NoteController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected Mutation Routes (Requires Authenticated Session)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Current authenticated user session details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Note mutations endpoints
    Route::post('/notes', [NoteController::class, 'store']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
    
    // Translation microservice route
    Route::post('/translate-snippet', [TranslationController::class, 'translateSnippet']);
});

