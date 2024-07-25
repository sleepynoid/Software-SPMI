<?php

namespace App\Http\Controllers;
use App\Http\Resources\PenetapanCollection;
use App\Models\Standar;

class testcontroller extends Controller {
    //
    public function welcome() {
        return view('welcome');
    }

    public function show() {

    }

    public function index() {
        // return Standar::all();
        return new PenetapanCollection(Standar::all());
    }
        
}
