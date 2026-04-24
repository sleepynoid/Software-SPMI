<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriStandar extends Model
{
    use HasFactory;

    protected $table = 'kategori_standar';
    protected $fillable = ['nama_kategori', 'is_default'];

    public function standarDiktis()
    {
        return $this->hasMany(StandarDikti::class, 'kategori_id');
    }
}
