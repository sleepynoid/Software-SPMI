<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaksanaan;
use App\Models\BuktiPelaksanaan;
use App\Models\Link;
use Illuminate\Support\Facades\Log;

class PelaksanaanController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->all(); // Ambil semua data yang dikirim

        Log::info($data);
        // Proses setiap standar yang diterima
        foreach ($data['standar'] as $standar) {
            if (isset($standar['indicators'])) { // Pastikan ada indikator sebelum proses
                foreach ($standar['indicators'] as $indicator) {
                    // Hanya simpan jika ada data yang cukup
                    if (isset($indicator['indicator']) && isset($indicator['target'])) {
                        // Cek apakah data pelaksanaan sudah ada
                        $pelaksanaan = Pelaksanaan::where('id_sheet', $data['id_sheet']) // Pastikan id_sheet dikirim
                            ->first();

                        if (!$pelaksanaan) {
                            // Jika data pelaksanaan belum ada, buat baru
                            $pelaksanaan = Pelaksanaan::create(['id_sheet' => $data['id_sheet']]);
                        }

                        // Cek apakah bukti pelaksanaan sudah ada
                        $buktiPelaksanaan = BuktiPelaksanaan::where('id_indikator', $indicator['id_indikator'])
                            ->where('id_pelaksanaan', $pelaksanaan->id)
                            ->first();

                        if ($buktiPelaksanaan) {
                            // Jika bukti pelaksanaan sudah ada, update
                            $buktiPelaksanaan->update([
                                'komentar' => $indicator['komentar'] ?? '', // Handle optional komentar
                            ]);
                        } else {
                            // Jika bukti pelaksanaan belum ada, buat baru
                            $buktiPelaksanaan = BuktiPelaksanaan::create([
                                'komentar' => $indicator['komentar'] ?? '',
                                'id_indikator' => $indicator['id_indikator'],
                                'id_pelaksanaan' => $pelaksanaan->id,
                            ]);
                        }

                        // Cek apakah link sudah ada
                        $link = Link::where('id_bukti_pelaksanaan', $buktiPelaksanaan->id)
                            ->first();

                        if ($link) {
                            // Jika link sudah ada, update
                            $link->update([
                                'judul_link' => $indicator['judul_link'] ?? '',
                                'link' => $indicator['link'] ?? '',
                            ]);
                        } else {
                            // Jika link belum ada, buat baru
                            Link::create([
                                'judul_link' => $indicator['judul_link'] ?? '',
                                'link' => $indicator['link'] ?? '',
                                'id_bukti_pelaksanaan' => $buktiPelaksanaan->id,
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }
}