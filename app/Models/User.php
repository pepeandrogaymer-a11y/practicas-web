<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password','role'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
            'two_factor_expires_at' => 'datetime',
        ];
    }
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
    public function isProfesor(): bool
{
    return $this->role === 'profesor';
}

public function isAlumno(): bool
{
    return $this->role === 'alumno';
}

public function student(): HasOne
{
    return $this->hasOne(Student::class);
}

public function isEstudiante(): bool
{
    return $this->role === 'estudiante';
}

public function reservations(): HasMany
{
    return $this->hasMany(Reservation::class);
}
}

;

