<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standar extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_penetapan',
        'note',
        'tipe',
    ];

    public function indikator() {
        return $this->hasMany(Indikator::class,'id_standar');
    }
}
