<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'fullname',
        'name',
        'user',
        'email',
        'password',
        'address',
        'phone',
        'avatar',
        'consultant_specialization_uuid',
        'code_ref',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->fullname;
    }

    public function getMembers()
    {
        return $this->where('code_ref', '!=', null)->get();
    }

    public function getconsultants()
    {
        return $this->where('consultant_code', '!=', null)->get();
    }

    //  relationship
    public function consultantSpecialization()
    {
        return $this->belongsTo(ConsultantSpecialization::class, 'consultant_specialization_uuid', 'uuid');
    }

    public function members()
    {
        return $this->hasMany(User::class, 'code_ref', 'consultant_code');
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'code_ref', 'consultant_code');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'consultant_specialization_uuid', 'consultant_specialization_uuid');
    }
}
