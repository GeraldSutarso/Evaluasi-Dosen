<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Evaluasi\EvaluasiController;
use App\Http\Controllers\Admin\AdminController;

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

//login or logout authentication routes
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('post-login', [AuthController::class, 'postLogin'])->name('login.post'); 
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

//Fallback
Route::fallback(function () {
    return redirect('/');
});

Route::middleware(['auth', 'admin'])->group(function () {
    // Admin Home Route (GET request)
    Route::get('/admin/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('admin/evaluation/{evaluation_id}', [AdminController::class, 'showEvaluationUsers'])->name('admin.showEvaluationUsers');

    // Admin Search Route (GET request)
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

});

//middleware check if user authenticated
Route::middleware(['auth.check'])->group(function () {
    //routes that require user to be authenticated
    
    //Home routes
    Route::get('home', [HomeController::class, 'home'])->name('home'); 
    Route::get('/', [HomeController::class, 'home']); 
    
    //the search bar
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    
    //Evaluation routes
    Route::get('/evaluation/{id}', [EvaluasiController::class, 'show'])->name('evaluation.show');
    Route::post('/evaluation/{id}/submit', [EvaluasiController::class, 'submitEvaluation'])->name('evaluation.submit');

    Route::get('ddsession', function(){
        return view('ddsession');
    })->name('ddsession');
    }
);