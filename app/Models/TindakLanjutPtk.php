<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjutPtk extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjut_ptk';
    protected $fillable = ['kka_id', 'akar_masalah', 'rencana_tindak_lanjut', 'jadwal_penyelesaian', 'status_verifikasi'];

    public function kertasKerjaAudit()
    {
        return $this->belongsTo(KertasKerjaAudit::class, 'kka_id');
    }
}
