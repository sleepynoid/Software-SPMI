<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelaksanaan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_sheet'
    ];

    public function BuktiPelaksanaan() {
        $this->hasMany(BuktiPelaksanaan::class,'id_pelaksanaan');
    }
}
