<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'parent_category', 
        'description', 
        'sort_order', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // หมวดหมู่หลัก (parent_category เป็น null)
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_category')->where('is_active', true)->orderBy('sort_order');
    }

    // หมวดหมู่ย่อย (parent_category ไม่เป็น null)
    public function scopeSubCategories($query, $parentCategory = null)
    {
        $query = $query->whereNotNull('parent_category')->where('is_active', true)->orderBy('sort_order');
        
        if ($parentCategory) {
            $query->where('parent_category', $parentCategory);
        }
        
        return $query;
    }

    // หมวดหมู่ย่อยของหมวดหมู่หลักนี้
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_category', 'name');
    }

    // หมวดหมู่หลักของหมวดหมู่ย่อยนี้
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category', 'name');
    }
}
