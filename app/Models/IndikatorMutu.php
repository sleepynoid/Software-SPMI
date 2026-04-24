<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorMutu extends Model
{
    use HasFactory;

    protected $table = 'indikator_mutu';
    protected $fillable = ['standar_id', 'kode_indikator', 'isi_standar', 'jenis'];

    public function standar()
    {
        return $this->belongsTo(StandarDikti::class, 'standar_id');
    }

    public function targetUnits()
    {
        return $this->hasMany(TargetUnit::class, 'indikator_id');
    }
}
