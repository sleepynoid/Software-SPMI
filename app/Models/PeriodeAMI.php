<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeAMI extends Model
{
    use HasFactory;

    protected $table = 'periode_ami';
    protected $fillable = ['tahun_akademik', 'tgl_mulai_audit', 'tgl_selesai_audit', 'status'];

    public function standarDiktis()
    {
        return $this->hasMany(StandarDikti::class, 'periode_id');
    }

    public function risalahRtms()
    {
        return $this->hasMany(RisalahRtm::class, 'periode_id');
    }
}
