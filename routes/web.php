<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Task\TaskController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('task');
// });
// Route::view('/tasks', 'task');


Route::get('/', function () {
    return view('task');
});
// Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
// Route::get('/tasks', [TaskController::class, 'getTasks']);

Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::DELETE('/destroy/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');