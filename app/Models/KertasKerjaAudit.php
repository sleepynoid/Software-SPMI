<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KertasKerjaAudit extends Model
{
    protected $table = 'kertas_kerja_audit';

    protected $fillable = ['capaian_id', 'auditor_id', 'kategori_temuan', 'deskripsi_temuan'];

    public function capaianPelaksanaan(): BelongsTo
    {
        return $this->belongsTo(CapaianPelaksanaan::class, 'capaian_id');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function tindakLanjut(): HasOne
    {
        return $this->hasOne(TindakLanjutPtk::class, 'kka_id');
    }
}
