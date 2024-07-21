<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sheet extends Model {
    use HasFactory;

    protected $fillable = [
        'jurusan',
        'periode',
        'note',
        'tipe_sheet'
    ];
}
