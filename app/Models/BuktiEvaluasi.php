<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiEvaluasi extends Model {
    use HasFactory;
    protected $fillable = [
        'adjustment',
        'komentar',
        'id_evaluasi',
        'id_bukti_pelaksanaan'
        
    ];
    public function evaluasi() {
        return $this->belongsTo(Evaluasi::class);
    }
    public function link() {
        return $this->hasMany(link::class,'id_bukti_pelaksanaan');
    }
}
