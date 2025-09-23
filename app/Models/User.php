<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // เพิ่ม role เข้ามา (จะใช้ในการกำหนดสิทธิ์ admin/user)
        'phone',
        'position',
        'department',
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

    // Relationships
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id'); // ผู้แจ้งปัญหา
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to_user_id'); // ผู้รับผิดชอบปัญหา
    }

    public function ticketUpdates(): HasMany
    {
        return $this->hasMany(TicketUpdate::class);
    }

    // Role Methods - ระบบ Role ใหม่
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCEO(): bool
    {
        return $this->role === 'ceo';
    }

    // Permission Methods - สิทธิ์ตาม Role ใหม่
    public function canManageTickets(): bool
    {
        return $this->isLeader() || $this->isManager() || $this->isCEO();
    }

    public function canViewAllTickets(): bool
    {
        return $this->isManager() || $this->isCEO();
    }

    public function canViewDashboard(): bool
    {
        return $this->isManager() || $this->isCEO();
    }

    public function canViewStrategicData(): bool
    {
        return $this->isCEO();
    }

    public function canViewTeamTickets(): bool
    {
        return $this->isLeader() || $this->isManager() || $this->isCEO();
    }

    // Backward compatibility methods (สำหรับโค้ดเก่า)
    public function isOwner(): bool
    {
        return $this->isCEO();
    }

    public function isHead(): bool
    {
        return $this->isManager();
    }

    public function isAgent(): bool
    {
        return $this->isLeader() || $this->isManager() || $this->isCEO();
    }

    // Notification relationships
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->whereNull('read_at');
    }

    public function readNotifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->whereNotNull('read_at');
    }
}
