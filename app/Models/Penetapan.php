<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penetapan extends Model
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

    public function standars() {
        return $this->hasMany(Standar::class,'id_penetapan');
    }
}
