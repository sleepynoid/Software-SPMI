<?php

namespace App\Http\Controllers\Penetapan;

use App\Exports\StandarTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\StandarImport;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportStandarController extends Controller
{
    public function index()
    {
        return Inertia::render('Penetapan/Import', [
            'periodes' => PeriodeAMI::where('status', '!=', 'Selesai')->orderBy('tahun_akademik', 'desc')->get(),
            'active_periode' => PeriodeAMI::whereNotIn('status', ['Draft', 'Selesai'])->latest()->first()
                ?? PeriodeAMI::where('status', '!=', 'Draft')->latest()->first()
                ?? PeriodeAMI::latest()->first(),
        ]);
    }

    public function template()
    {
        return Excel::download(new StandarTemplateExport, 'template_import_standar.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'periode_id' => 'required|exists:periode_ami,id',
        ]);

        $periodeId = $request->input('periode_id');

        Log::info('Import standar started', ['periode_id' => $periodeId, 'file' => $request->file('file')->getClientOriginalName(), 'ip' => $request->ip()]);

        try {
            Excel::import(new StandarImport($periodeId), $request->file('file'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors());
            }

            Log::warning('Import standar failed validation', ['errors' => $errors, 'ip' => $request->ip()]);

            return redirect()->back()->withErrors(['file' => $errors])->withInput();
        }

        Log::info('Import standar completed', ['periode_id' => $periodeId, 'ip' => $request->ip()]);

        return redirect()->route('penetapan.standar.index')->with('success', 'Data standar, indikator, dan target berhasil diimport.');
    }
}
