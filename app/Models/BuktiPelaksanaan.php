<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPelaksanaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_link',
        'link',
        'id_pelaksanaan',
    ];

    public function pelaksanaan() {
        return $this->belongsTo(Pelaksanaan::class);
    }
}
