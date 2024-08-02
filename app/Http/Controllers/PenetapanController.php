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
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);
            Excel::import(new PenetapanImport, $request->file('file'));
            $request->file('file')->store('uploads', 'public');
            return redirect('/')->with('success', 'Data berhasil diimpor');
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
            return back()->withErrors(['errors' => $errorMessages]);
        } catch (\Exception $e) {
            return back()->withErrors('Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}