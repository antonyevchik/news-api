<?php

use App\Http\Controllers\PostsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->prefix('posts')->group(function () {
    Route::get('/', [PostsController::class, 'index'])->name('posts.index');
    Route::get('/find_by_id/{post}', [PostsController::class, 'findById'])->name('posts.find-by-id');
    Route::post('/', [PostsController::class, 'store'])->name('posts.store');
    Route::put('/{post}', [PostsController::class, 'update'])->name('posts.update');
    Route::delete('/{post}', [PostsController::class, 'destroy'])->name('posts.destroy');
});