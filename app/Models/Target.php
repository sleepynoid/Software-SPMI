<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_indikator',
        'value',
    ];

    public function indikator() {
        return $this->belongsTo(Indikator::class);
    }
}
