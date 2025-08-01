<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

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

    // Helper for role
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    // ตรวจสอบว่าเป็น "หัวหน้าระบบ" หรือไม่
    public function isHead(): bool
    {
        return $this->role === 'head';
    }

    // ตรวจสอบว่าเป็น "เจ้าหน้าที่" หรือไม่ (ผู้ที่ได้รับมอบหมายงานได้)
    public function isAgent(): bool
    {
        return $this->role === 'agent' || $this->role === 'head' || $this->role === 'owner'; // Owner และ Head ก็ถือเป็น Agent ได้ในที่นี้
    }

    // ตรวจสอบว่ามีสิทธิ์ในการจัดการ Ticket ได้หรือไม่ (เช่น เจ้าของ, หัวหน้า, เจ้าหน้าที่)
    public function canManageTickets(): bool
    {
        return $this->isOwner() || $this->isHead() || $this->isAgent();
    }
}