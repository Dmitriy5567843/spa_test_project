<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('/', [CommentController::class, 'create'])->name('comment.create');
Route::get('/', [CommentController::class, 'index'])->name('comment.index');
Route::post('/store', [CommentController::class, 'store'])->name('comments.store');
Route::get('/download/{name}', [CommentController::class, 'download'])->name('download');
