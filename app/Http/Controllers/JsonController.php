<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function index(){
        $sheets = Sheet::all();


        return response()->json($sheets);
    }
}
