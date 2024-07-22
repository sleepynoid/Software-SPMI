<?php

namespace App\Http\Controllers;

use App\Models\Standar;
use Illuminate\Http\Request;

class PenetapanController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
        return Standar::all();
        // $listStandar[] = Standar::all();
        // return response()->json([
        //     ,
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
