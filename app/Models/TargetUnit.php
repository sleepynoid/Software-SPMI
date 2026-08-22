<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TargetUnit extends Model
{
    protected $table = 'target_unit';

    protected $fillable = ['indikator_id', 'unit_kerja_id', 'nilai_target', 'satuan'];

    public function indikatorMutu(): BelongsTo
    {
        return $this->belongsTo(IndikatorMutu::class, 'indikator_id');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function capaianPelaksanaan(): HasOne
    {
        return $this->hasOne(CapaianPelaksanaan::class, 'target_unit_id');
    }
}
