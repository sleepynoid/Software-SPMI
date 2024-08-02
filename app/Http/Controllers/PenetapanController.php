<?php

namespace App\Http\Controllers;

use App\Imports\PenetapanImport;
use App\Models\Standar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class PenetapanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return nested json standar -> indikator -> target
        // dengan 'indikator.target' sebagai indikator dari nama method class standar
        // dan target dari indikator
        return Standar::with('indikator.target')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // pending
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        //
        return Standar::with('indikator.target')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // pending
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // pending
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv',
                'jurusan' => 'required',
                'tipe' => 'required',
                'periode' => 'required',
                'note' => 'nullable'
            ]);

            $sheet = [
                'jurusan' => $request->jurusan,
                'tipe_sheet' => $request->tipe,
                'periode' => $request->periode,
                'note' => $request->note
            ];

            $fileName = $request->jurusan . '_' . $request->tipe . '_' . $request->periode . '.xlsx';
            Excel::import(new PenetapanImport($sheet), $request->file('file'));
            $request->file('file')->storeAs('uploads', $fileName, 'public');
            return response()->json(['success' => true, 'message' => 'Data berhasil diimpor', 'redirect' => '/sheet']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                foreach ($failure->errors() as $error) {
                    $errorMessages[] = $error;
                    $request->session()->flash('error', $error);
                    Log::error($error);
                }
            }
            return response()->json(['success' => false, 'message' => 'Data gagal diimpor', 'errors' => $errorMessages]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()]);
        }
    }
}