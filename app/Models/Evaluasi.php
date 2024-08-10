<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_sheet',
    ];
    public function BuktiEvaluasi() {
        $this->hasMany(BuktiEvaluasi::class,'id_evaluasi');
    }
}
