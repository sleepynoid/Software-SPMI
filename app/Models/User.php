<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'nidn',
        'jenis_user',
        'role_id',
        'unit_kerja_id',
        'email',
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function isAdmin()
    {
        return $this->role?->nama_role === 'Admin/LPM';
    }

    public function isAuditor()
    {
        return $this->role?->nama_role === 'Auditor';
    }

    public function isAuditee()
    {
        return $this->role?->nama_role === 'Auditee';
    }

    public function isPimpinan()
    {
        return $this->role?->nama_role === 'Pimpinan';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
