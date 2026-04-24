<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja';
    protected $fillable = ['nama_unit', 'jenis_unit', 'kepala_unit_id'];

    public function kepalaUnit()
    {
        return $this->belongsTo(User::class, 'kepala_unit_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function targetUnits()
    {
        return $this->hasMany(TargetUnit::class);
    }

    public function risalahRtms()
    {
        return $this->hasMany(RisalahRtm::class);
    }
}
