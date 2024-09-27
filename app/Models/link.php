<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class link extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'judul_link',
        'link',
        'id_bukti',
        'tipe_link'
    ];
}
