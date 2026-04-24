<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetUnit extends Model
{
    use HasFactory;

    protected $table = 'target_unit';
    protected $fillable = ['indikator_id', 'unit_kerja_id', 'nilai_target', 'satuan'];

    public function indikatorMutu()
    {
        return $this->belongsTo(IndikatorMutu::class, 'indikator_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function capaianPelaksanaan()
    {
        return $this->hasOne(CapaianPelaksanaan::class, 'target_unit_id');
    }
}
