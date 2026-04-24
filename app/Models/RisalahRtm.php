<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RisalahRtm extends Model
{
    use HasFactory;

    protected $table = 'risalah_rtm';
    protected $fillable = ['periode_id', 'unit_kerja_id', 'tanggal_rapat', 'hasil_pembahasan', 'rekomendasi_standar'];

    public function periode()
    {
        return $this->belongsTo(PeriodeAMI::class, 'periode_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }
}
