<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'priority_id',
        'status_id',
        'title',
        'description',
        'assigned_to_user_id',
        'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo // ผู้แจ้งปัญหา
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function assignedTo(): BelongsTo // ผู้รับผิดชอบ
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TicketUpdate::class);
    }
}
