<?php

use App\Http\Controllers\NodeController;
use App\Http\Controllers\testcontroller;
use Illuminate\Support\Facades\Route;

 Route::get('/', function () {
      return view('welcome');
 });

 Route::get('/{pathMatch}', function(){
     return view('welcome');
 })->where('pathMatch',".*");
//Route::get('/',[testcontroller::class, 'welcome']);
// Route::get('/api',[testcontroller::class,'show']);
// Route::get('/sheet/create',[NodeController::class,'create']);
// Route::get('/sheet',[NodeController::class,'store']);
// Route::get('/sheet/{id}',[NodeController::class,'show']);
// Route::get('/sheet/{id}/edit',[NodeController::class,'edit']);
// Route::put('/sheet/{id}',[NodeController::class,'update']);
// Route::delete('/sheet/{id}',[NodeController::class,'destroy']);
//Route::apiResource('test',NodeController::class);
