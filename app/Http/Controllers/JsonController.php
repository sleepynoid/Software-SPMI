<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function index(){
        $data = [
            [
                'standar' => 'Standar 1',
                'indicators' => [
                    ['indicator' => 'Indicator 1.1', 'target' => 'Target 1.1'],
                    ['indicator' => 'Indicator 1.2', 'target' => 'Target 1.2']
                ]
            ],
            [
                'standar' => 'Standar 2',
                'indicators' => [
                    ['indicator' => 'Indicator 2.1', 'target' => 'Target 2.1'],
                    ['indicator' => 'Indicator 2.2', 'target' => 'Target 2.2'],
                    ['indicator' => 'Indicator 2.3', 'target' => 'Target 2.3']
                ]
            ]
        ];


        return response()->json($data);
    }

    public function GetSheet(){
        $datas = [
            [
                'standar' => 'Standar 1',
                'indicators' => [
                    ['indicator' => 'Indicator 1.1', 'target' => 'Target 1.1', 'komen' => 'komentarr'],
                    ['indicator' => 'Indicator 1.2', 'target' => 'Target 1.2', 'komen' => 'komentar 1.2']
                ]
            ],
            [
                'standar' => 'Standar 1',
                'indicators' => [
                    ['indicator' => 'Indicator 1.1', 'target' => 'Target 1.1', 'komen' => 'komentarr 2.1'],
                    ['indicator' => 'Indicator 1.2', 'target' => 'Target 1.2', 'komen' => 'komentarr']
                ]
            ],
            [
                'standar' => 'Standar 2',
                'indicators' => [
                    ['indicator' => 'Indicator 2.1', 'target' => 'Target 2.1', 'komen' => 'komentarr'],
                    ['indicator' => 'Indicator 2.2', 'target' => 'Target 2.2', 'komen' => 'komentarr'],
                    ['indicator' => 'Indicator 2.3', 'target' => 'Target 2.3', 'komen' => 'komentarr'],
                    ['indicator' => 'Indicator 2.4', 'target' => 'Target 2.4', 'komen' => 'komentarr']
                ]
            ]
        ];

        $data = [
            'standar' => 'Standar 3',
            'indicators' => [
                ['indicator' => 'Indicator 1.1', 'target' => 'Target 1.1', 'komen' => 'komentarr'],
                ['indicator' => 'Indicator 1.2', 'target' => 'Target 1.2', 'komen' => 'komentarr']
            ]
        ];

        $newIndicator = ['indicator' => 'Indicator 1.3', 'target' => 'Target 1.3', 'komen'=>'Ireland'];
        array_push($data['indicators'], $newIndicator);

        array_push($datas, $data);


        return view('welcome', ['name' => $datas]);
    }


}
