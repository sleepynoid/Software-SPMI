<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_standar',
        'note',
    ];

    public function standar() {
        return $this->belongsTo(Standar::class);
    }
    public function target() {
        return $this->hasMany(Target::class,'id_indikator');
    }
}
