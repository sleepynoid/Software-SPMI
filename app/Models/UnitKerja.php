<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';

    protected $fillable = ['nama_unit', 'jenis_unit', 'kepala_unit_id'];

    public function kepalaUnit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kepala_unit_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function targetUnits(): HasMany
    {
        return $this->hasMany(TargetUnit::class);
    }

    public function risalahRtms(): HasMany
    {
        return $this->hasMany(RisalahRtm::class);
    }
}
