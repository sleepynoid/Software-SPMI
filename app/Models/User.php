<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama_lengkap
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $nidn
 * @property string|null $jenis_user
 * @property int|null $role_id
 * @property int|null $unit_kerja_id
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Role|null $role
 * @property-read UnitKerja|null $unitKerja
 */
#[Fillable(['nama_lengkap', 'email', 'password', 'nidn', 'jenis_user', 'role_id', 'unit_kerja_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->nama_role === 'Admin/LPM';
    }

    public function isAuditor(): bool
    {
        return $this->role?->nama_role === 'Auditor';
    }

    public function isAuditee(): bool
    {
        return $this->role?->nama_role === 'Auditee';
    }

    public function isPimpinan(): bool
    {
        return $this->role?->nama_role === 'Pimpinan';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
