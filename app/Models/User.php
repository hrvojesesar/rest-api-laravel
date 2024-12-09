<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'roles' => $this->roles->pluck('name')->toArray(), // Generiši niz uloga
            'permissions' => $this->permissions()->pluck('name')->toArray(), // Generiši niz dozvola
        ];
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    // Provjera ima li korisnik određenu rolu
    /**
     * Summary of hasRole
     * @param mixed $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Provjera ima li korisnik određenu dozvolu
    public function hasPermission($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }

    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique();
    }

    // dodijeli korisniku ulogu (argument ime uloge)
    public function assignRole($role)
    {
        $role = Role::where('name', $role)->first();

        if (!$role) {
            return false;
        }

        return $this->roles()->syncWithoutDetaching($role->id);
    }

    // dodijeli korisniku dozvolu (argument ime dozvole)
    public function assignPermission($permission)
    {
        $permission = Permission::where('name', $permission)->first();

        if (!$permission) {
            return false;
        }

        return $this->permissions()->syncWithoutDetaching($permission->id);
    }

    // ukloni korisniku ulogu (argument ime uloge)
    public function revokeRole($role)
    {
        $role = Role::where('name', $role)->first();

        if (!$role) {
            return false;
        }

        return $this->roles()->detach($role->id);
    }

    // ukloni korisniku dozvolu (argument ime dozvole)
    public function revokePermission($permission)
    {
        $permission = Permission::where('name', $permission)->first();

        if (!$permission) {
            return false;
        }

        return $this->permissions()->detach($permission->id);
    }
}
