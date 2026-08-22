<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriStandar extends Model
{
    protected $table = 'kategori_standar';

    protected $fillable = ['nama_kategori', 'is_default'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function standarDiktis(): HasMany
    {
        return $this->hasMany(StandarDikti::class, 'kategori_id');
    }
}
