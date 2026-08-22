<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CapaianPelaksanaan extends Model
{
    protected $table = 'capaian_pelaksanaan';

    protected $fillable = ['target_unit_id', 'nilai_aktual', 'evaluasi_diri', 'link_dokumen_bukti', 'submitted_by', 'submitted_at'];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function targetUnit(): BelongsTo
    {
        return $this->belongsTo(TargetUnit::class, 'target_unit_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function kertasKerjaAudit(): HasOne
    {
        return $this->hasOne(KertasKerjaAudit::class, 'capaian_id');
    }
}
