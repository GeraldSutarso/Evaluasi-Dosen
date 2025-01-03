<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Evaluasi\EvaluasiController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Modifier\ImportController;
use App\Http\Controllers\Modifier\ExportController;

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
//2 factor verif auth
Route::get('2fa', [AuthController::class, 'show2faForm'])->name('2fa.form');
Route::post('2fa', [AuthController::class, 'verify2fa'])->name('2fa.verify');

//login or logout authentication routes
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('post-login', [AuthController::class, 'postLogin'])->name('login.post'); 
Route::get('logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'admin', 'log.ip','log.user','2fa'])->group(function () {
    // Admin Home Route (GET request)
    Route::get('/admin/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('admin/evaluation/{evaluation_id}', [AdminController::class, 'showEvaluationUsers'])->name('admin.showEvaluationUsers');
    Route::get('admin/evaluation/groups/{matkul_id}/{lecturer_id}', [AdminController::class, 'showEvaluationGroups'])->name('admin.evaluation.groups');
    Route::get('admin/evaluation/group/{group_id}/users/{matkul_id}/{lecturer_id}', [AdminController::class, 'showGroupUsers'])->name('admin.evaluation.group.users');
    //download PDF
    Route::get('admin/evaluation/summary/{matkulId}/{lecturerId}/pdf', [EvaluasiController::class, 'downloadPDF'])->name('evaluation.summary.pdf');
    Route::get('admin/evaluation/summary/TPMO/{matkulId}/{lecturerId}/pdf', [EvaluasiController::class, 'downloadTPMO'])->name('evaluation.summaryTPMO.pdf');
    Route::get('admin/evaluation/summary/TOPKR/{matkulId}/{lecturerId}/pdf', [EvaluasiController::class, 'downloadTOPKR'])->name('evaluation.summaryTOPKR.pdf');
    // Admin Search Route (GET request)
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
    //summary
    Route::get('admin/evaluation/summary/{matkulId}/{lecturerId}', [EvaluasiController::class, 'calculateSummary'])
    ->name('evaluation.summary');
    //summary TPMO
    Route::get('admin/evaluation/summary/TPMO/{matkulId}/{lecturerId}', [EvaluasiController::class, 'calculateSummaryTPMO'])
    ->name('evaluation.summaryTPMO');
    //summary TOPKR
    Route::get('admin/evaluation/summary/TOPKR/{matkulId}/{lecturerId}', [EvaluasiController::class, 'calculateSummaryTOPKR'])
    ->name('evaluation.summaryTOPKR');

    //Excel database management:
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'import'])->name('import.process');
    Route::get('/export', [ExportController::class, 'exportDatabase'])->name('export.process');
    Route::get('/modify', [AdminController::class,'modify'])->name('admin.modify');

    //Fallback
    Route::fallback(function () {
        return redirect('/');
    });

});

// Route::middleware(['log.ip'])->group(function () {
//     // Protected routes
//     Route::get('/home', [HomeController::class, 'index'])->name('home');
//     // Add other routes here
// });


//middleware check if user authenticated
Route::middleware(['auth','auth.check', 'log.ip', 'log.user'])->group(function () {
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

