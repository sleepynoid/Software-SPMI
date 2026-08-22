<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StandarDikti extends Model
{
    protected $table = 'standar_dikti';

    protected $fillable = ['periode_id', 'kategori_id', 'nama_standar'];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeAMI::class, 'periode_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriStandar::class, 'kategori_id');
    }

    public function indikatorMutus(): HasMany
    {
        return $this->hasMany(IndikatorMutu::class, 'standar_id');
    }
}
