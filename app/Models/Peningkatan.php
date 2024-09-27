<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peningkatan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_pengendalian',
        'komentar',
        'edited_by',
    ];
}
