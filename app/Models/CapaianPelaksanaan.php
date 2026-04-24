<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianPelaksanaan extends Model
{
    use HasFactory;

    protected $table = 'capaian_pelaksanaan';
    protected $fillable = ['target_unit_id', 'nilai_aktual', 'evaluasi_diri', 'link_dokumen_bukti', 'submitted_by', 'submitted_at'];

    public function targetUnit()
    {
        return $this->belongsTo(TargetUnit::class, 'target_unit_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function kertasKerjaAudit()
    {
        return $this->hasOne(KertasKerjaAudit::class, 'capaian_id');
    }
}
