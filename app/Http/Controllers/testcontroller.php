<?php

namespace App\Http\Controllers;

use App\Http\Resources\PenetapanResource;
use App\Http\Resources\StandarResource;
use App\Models\Standar;
// use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class testcontroller extends Controller {
    //
    public function welcome() {
        return view('welcome');
    }

    public function show(int $id): JsonResponse {
        $data = Standar::with('indikator.target')->findOrFail($id);
        if (is_null($data)) {
            $this->sendError('id not found');
        }
        // $responData = new StandarResource($data);
        // return $this->sendRespons($responData,'Standar');
        // return ;
    }

    public function index(): JsonResponse {
        $data = Standar::with('indikator.target')->get();
        // $responData = StandarResource::collection($data);
        return $this->sendRespons($data,'Standar');
    }

    public function getSheet() {
        
    }
}
