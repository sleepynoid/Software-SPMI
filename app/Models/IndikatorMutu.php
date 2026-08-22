<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndikatorMutu extends Model
{
    protected $table = 'indikator_mutu';

    protected $fillable = ['standar_id', 'kode_indikator', 'isi_standar', 'jenis'];

    public function standar(): BelongsTo
    {
        return $this->belongsTo(StandarDikti::class, 'standar_id');
    }

    public function targetUnits(): HasMany
    {
        return $this->hasMany(TargetUnit::class, 'indikator_id');
    }
}
