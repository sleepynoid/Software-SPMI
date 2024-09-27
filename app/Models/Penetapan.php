<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penetapan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_sheet'
    ];

    public function standar() {
        return $this->hasMany(Standar::class,'id_penetapan');
    }
}
