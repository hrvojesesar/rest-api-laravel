<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'categories' => $categories
        ], 200);
    }

    public function getCategoryById($id)
    {
        $category = Category::where('CategoryID', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully.',
            'category' => $category
        ], 200);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'CategoryName' => 'required|string|unique:categories|max:15',
            'Description' => 'nullable|string|max:65535'
        ]);

        $category = Category::create([
            'CategoryName' => $request->CategoryName,
            'Description' => $request->Description
        ]);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not created.'
            ], 500);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Category created successfully.',
            'category' => $category
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::where('CategoryID', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $request->validate([
            'CategoryName' => 'required|string|max:15',
            'Description' => 'nullable|string|max:65535'
        ]);

        $category->update([
            'CategoryName' => $request->CategoryName,
            'Description' => $request->Description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'category' => $category
        ], 200);
    }

    public function deleteCategory($id)
    {
        $category = Category::where('CategoryID', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}
