<?php

namespace App\Http\Controllers;

use App\Imports\PenetapanImport;
use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Routing\Controller;

class PenetapanController extends Controller {
    //    public function __construct() {
//        $this->middleware('auth:sanctum');
//    }

    public function getPenetapan(Request $request) {

    }

    public function import(Request $request) {
        try {
            $this->validateRequest($request);

            $sheet = $this->prepareSheetData($request);
            $fileName = $this->createFileName($request);

            Excel::import(new PenetapanImport($sheet), $request->file('file'));
            $request->file('file')->storeAs('uploads', $fileName, 'public');

            return response()->json(['success' => true, 'message' => 'Data berhasil diimpor']);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e, $request);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()]);
        }
    }

    private function validateRequest(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'jurusan' => 'required',
            'tipe' => 'required',
            'periode' => 'required',
            'note' => 'nullable'
        ]);
    }

    private function prepareSheetData(Request $request) {
        return [
            'jurusan' => $request->jurusan,
            'tipe_sheet' => $request->tipe,
            'periode' => $request->periode,
            'note' => $request->note
        ];
    }

    private function createFileName(Request $request) {
        return $request->jurusan . '_' . $request->tipe . '_' . $request->periode . '.xlsx';
    }

    private function handleValidationException(ValidationException $e, Request $request) {
        $errorMessages = [];
        foreach ($e->failures() as $failure) {
            foreach ($failure->errors() as $error) {
                $errorMessages[] = $error;
                $request->session()->flash('error', $error);
            }
        }
        return response()->json(['success' => false, 'message' => 'Data gagal diimpor', 'errors' => $errorMessages]);
    }

    public static function getAll($id_penetapan) {
        $penetapan = Penetapan::find($id_penetapan);
        if (!$penetapan) {
            Log::info('id penetapan not found');
        }
        $standar = Standar::where('id_penetapan', $id_penetapan)->get();
        $indikator[] = null;
        $target[] = null;
        Log::info($standar);
        if ($standar) {
            Log::warning('loop standar');
            foreach ($standar as $stan) {
                Log::info($stan['id']);
                // $indikator = Indikator::where('id_standar', $stan->id)->get();
                // Log::info(json_encode($indikator));
            }
        }
        $bukti_pelaksanaan = null;
        Log::warning(json_encode($indikator));
        if ($indikator) {
            Log::warning('loop indikator');
            foreach ($indikator as $indika) {
                Log::info($indika->id);
                $target = Target::where('id_indikator', $indika->id)->get();
                Log::info(json_encode($target));
                $bukti_pelaksanaan = BuktiPelaksanaan::where('id_indikator', $indika->id)->get();
                Log::info(json_encode($bukti_pelaksanaan));
            }
        }

    }
}
