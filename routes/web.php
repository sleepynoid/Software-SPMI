<?php

use App\Http\Controllers\NodeController;
use App\Http\Controllers\PenetapanController;
use App\Http\Controllers\testcontroller;
use App\Http\Controllers\JsonController;
use Illuminate\Support\Facades\Route;

Route::get('/', [JsonController::class, 'GetSheet']);
Route::view('/upload','upload');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

Route::post('/penetapan/import', [PenetapanController::class, 'import'])->name('penetapan.import');
// Route::get('/{pathMatch}', function(){
//     return view('welcome');
// })->where('pathMatch',".*");

// Route::get('/api/sheet',[JsonController::class, 'index']);
//Route::get('/',[testcontroller::class, 'welcome']);
// Route::get('/api',[testcontroller::class,'show']);
// Route::get('/sheet/create',[NodeController::class,'create']);
// Route::get('/sheet',[NodeController::class,'store']);
// Route::get('/sheet/{id}',[NodeController::class,'show']);
// Route::get('/sheet/{id}/edit',[NodeController::class,'edit']);
// Route::put('/sheet/{id}',[NodeController::class,'update']);
// Route::delete('/sheet/{id}',[NodeController::class,'destroy']);
//Route::apiResource('test',NodeController::class);
