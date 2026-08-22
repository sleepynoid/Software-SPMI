<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeAMI extends Model
{
    protected $table = 'periode_ami';

    protected $fillable = ['tahun_akademik', 'tgl_mulai_audit', 'tgl_selesai_audit', 'status'];

    protected function casts(): array
    {
        return [
            'tgl_mulai_audit' => 'date',
            'tgl_selesai_audit' => 'date',
        ];
    }

    public function standarDiktis(): HasMany
    {
        return $this->hasMany(StandarDikti::class, 'periode_id');
    }

    public function risalahRtms(): HasMany
    {
        return $this->hasMany(RisalahRtm::class, 'periode_id');
    }
}
