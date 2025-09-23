<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * ดึงหมวดหมู่ย่อยตามหมวดหมู่หลัก
     */
    public function getSubCategories($mainCategoryName)
    {
        try {
            // Decode URL parameter
            $decodedCategoryName = urldecode($mainCategoryName);
            
            $subCategories = Category::whereNotNull('parent_category')
                ->where('parent_category', $decodedCategoryName)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            return response()->json($subCategories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}