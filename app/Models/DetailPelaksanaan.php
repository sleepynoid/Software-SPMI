<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPelaksanaan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id_indikator',
        'komentar',
        'id_pelaksanaan',
    ];
}
