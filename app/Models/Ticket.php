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
        'images',
        'primary_image',
        'image_count',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'images' => 'array',
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

    public function workflows(): HasMany
    {
        return $this->hasMany(TicketWorkflow::class);
    }

    public function activeWorkflow(): HasMany
    {
        return $this->hasMany(TicketWorkflow::class)->where('status', 'running');
    }

    // Image management methods
    public function addImage($imagePath)
    {
        $images = $this->images ?? [];
        $images[] = $imagePath;
        
        $this->images = $images;
        $this->image_count = count($images);
        
        // Set first image as primary if none exists
        if (!$this->primary_image) {
            $this->primary_image = $imagePath;
        }
        
        $this->save();
    }

    public function removeImage($imagePath)
    {
        $images = $this->images ?? [];
        $images = array_filter($images, function($path) use ($imagePath) {
            return $path !== $imagePath;
        });
        
        $this->images = array_values($images);
        $this->image_count = count($images);
        
        // Update primary image if removed
        if ($this->primary_image === $imagePath) {
            $this->primary_image = !empty($images) ? $images[0] : null;
        }
        
        $this->save();
    }

    public function setPrimaryImage($imagePath)
    {
        if (in_array($imagePath, $this->images ?? [])) {
            $this->primary_image = $imagePath;
            $this->save();
        }
    }

    public function getImageUrls()
    {
        if (!$this->images) return [];
        
        $validImages = [];
        foreach($this->images as $path) {
            // ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
            if ($this->imageFileExists($path)) {
                $validImages[] = asset('storage/' . str_replace('public/', '', $path));
            }
        }
        
        return $validImages;
    }

    public function getPrimaryImageUrl()
    {
        if (!$this->primary_image) return null;
        
        // ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
        if (!$this->imageFileExists($this->primary_image)) {
            return null;
        }
        
        return asset('storage/' . str_replace('public/', '', $this->primary_image));
    }

    private function imageFileExists($path)
    {
        try {
            $storagePath = str_replace('public/', '', $path);
            return \Storage::disk('public')->exists($storagePath);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasImages()
    {
        return count($this->getImageUrls()) > 0;
    }
}
