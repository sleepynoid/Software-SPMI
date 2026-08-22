<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RisalahRtm extends Model
{
    protected $table = 'risalah_rtm';

    protected $fillable = ['periode_id', 'unit_kerja_id', 'tgl_rtm', 'pimpinan_rapat', 'isi_risalah', 'keputusan_peningkatan'];

    protected function casts(): array
    {
        return [
            'tgl_rtm' => 'date',
        ];
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeAMI::class, 'periode_id');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }
}
