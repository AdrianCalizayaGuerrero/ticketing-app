<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);

        return response()->json($categories);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expected_response_time' => 'required|integer|min:0',
            'expected_resolution_time' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $category = Category::create($data);

        return response()->json($category, 201);
    }


    public function show(Category $category)
    {
        return response()->json(
            $category->load('tickets')
        );
    }


    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'expected_response_time' => 'sometimes|integer|min:0',
            'expected_resolution_time' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $category->update($data);

        return response()->json($category);
    }


    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}
