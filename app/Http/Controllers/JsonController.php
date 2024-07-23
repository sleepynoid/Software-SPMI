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

    public function getStandard(){
        $standard = [
            ['idS' => '1', 'note' => 'Lulus 3.5 abad'],
            ['idS' => '2', 'note' => 'item2'],
            ['idS' => '3', 'note' => 'item3']
        ];


        return response()->json($standard);
    }

    public function getIndicator(){
        $indicator = [
            ['idI' => '1', 'idS'=> 1,'note' => 'Lulus 3.5 abad'],
            ['idI' => '2', 'idS'=> 1,'note' => 'item2'],
            ['idI' => '3', 'idS'=> 1,'note' => 'item3'],
            ['idI' => '4', 'idS'=> 2,'note' => 'item4'],
            ['idI' => '5', 'idS'=> 2,'note' => 'item5'],
            ['idI' => '6', 'idS'=> 2,'note' => 'item6'],
            ['idI' => '7', 'idS'=> 3,'note' => 'item7'],
            ['idI' => '8', 'idS'=> 3,'note' => 'item8'],
            ['idI' => '9', 'idS'=> 3,'note' => 'item9'],
        ];


        return response()->json($indicator);
    }

    public function getTarget(){
        $target = [
            ['idT' => '1', 'idI'=> 1,'note' => 'Lulus 3.5 abad'],
            ['idT' => '2', 'idI'=> 1,'note' => 'item2'],
            ['idT' => '3', 'idI'=> 1,'note' => 'item3'],
            ['idT' => '4', 'idI'=> 2,'note' => 'item4'],
            ['idT' => '5', 'idI'=> 2,'note' => 'item5'],
            ['idT' => '6', 'idI'=> 2,'note' => 'item6'],
            ['idT' => '7', 'idI'=> 3,'note' => 'item7'],
            ['idT' => '8', 'idI'=> 3,'note' => 'item8'],
            ['idT' => '9', 'idI'=> 3,'note' => 'item9'],
        ];


        return response()->json($target);
    }


}
