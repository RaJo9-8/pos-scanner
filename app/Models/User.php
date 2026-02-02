<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function getLevelNameAttribute()
    {
        $levels = [
            1 => 'Super Admin',
            2 => 'Admin',
            3 => 'Leader',
            4 => 'Kasir',
            5 => 'Manager',
        ];
        
        return $levels[$this->level] ?? 'Unknown';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function returnTransactions()
    {
        return $this->hasMany(ReturnTransaction::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isSuperAdmin()
    {
        return (int)$this->level === 1;
    }

    public function isAdmin()
    {
        return $this->level === 2;
    }

    public function isLeader()
    {
        return $this->level === 3;
    }

    public function isKasir()
    {
        return $this->level === 4;
    }

    public function isManager()
    {
        return $this->level == 5; // menggunakan == bukan === untuk handle string/int
    }

    public function canViewProducts()
    {
        return in_array($this->level, [1, 2, 3, 4, 5]);
    }

    public function canManageProducts()
    {
        return in_array($this->level, [1, 2, 3]);
    }

    public function canAccessAllData()
    {
        return in_array($this->level, [1, 2]);
    }

    public function canManageTransactions()
    {
        return in_array($this->level, [3, 4]);
    }

    public function canManageReturns()
    {
        return $this->level == 3; // menggunakan == bukan === untuk handle string/int
    }

    public function canViewReports()
    {
        return $this->level === 5;
    }

    public function canViewActivityLogs()
    {
        return in_array($this->level, [1, 2]);
    }

    public function canRestoreDeletedData()
    {
        return $this->level === 1;
    }
}
