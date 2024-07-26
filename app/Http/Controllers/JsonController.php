<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function index(){
        $penetapan = Penetapan::find(1);
        $standars = Standar::where('id_penetapan', $penetapan->id);
        $indikator = Indikator::all();
        $target = Target::all();

        $response = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_id_standar == $s->id){
                    $newIndicator = ['indicator' => $i->note, 'target' => 'Target 1.3', 'komen'=>'Ireland'];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($response, $data);
        }

        return response()->json($response);
    }

    public function GetSheet(){
        $penetapan = Penetapan::find(1);
        $standars = Standar::where('id_penetapan', $penetapan->id)->get();
        $indikator = Indikator::all();
        $target = Target::all();

        $response = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_standar == $s->id){
                    $tar = null;
                    foreach ($target as $t){
                        if ($t->id == $i->id){
                            $tar = $t;
                        }
                    }

                    $newIndicator = ['indicator' => $i->note, 'target' => $tar->value, 'komen'=>'Ireland'];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($response, $data);
        }


        return view('welcome', ['name' => $response]);
    }

    public function aaa(){
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
