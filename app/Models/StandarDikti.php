<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandarDikti extends Model
{
    use HasFactory;

    protected $table = 'standar_dikti';
    protected $fillable = ['periode_id', 'kategori_id', 'nama_standar'];

    public function periode()
    {
        return $this->belongsTo(PeriodeAMI::class, 'periode_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriStandar::class, 'kategori_id');
    }

    public function indikatorMutus()
    {
        return $this->hasMany(IndikatorMutu::class, 'standar_id');
    }
}
