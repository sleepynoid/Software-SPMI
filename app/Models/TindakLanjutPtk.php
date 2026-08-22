<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjutPtk extends Model
{
    protected $table = 'tindak_lanjut_ptk';

    protected $fillable = ['kka_id', 'akar_masalah', 'rencana_tindak_lanjut', 'jadwal_penyelesaian', 'status_verifikasi'];

    protected function casts(): array
    {
        return [
            'jadwal_penyelesaian' => 'date',
        ];
    }

    public function kertasKerjaAudit(): BelongsTo
    {
        return $this->belongsTo(KertasKerjaAudit::class, 'kka_id');
    }
}
