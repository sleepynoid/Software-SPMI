<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelaksanaan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_sheet',
        'status',
        'submitted_at',
        'submitted_by',
        'catatan'
    ];

    public function sheet() {
        return $this->belongsTo(Sheet::class, 'id_sheet');
    }

    public function buktiPelaksanaans() {
        return $this->hasMany(BuktiPelaksanaan::class,'id_pelaksanaan');
    }
}
