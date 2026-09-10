<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CATEGORY LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Category::withCount('products');

        /*
        |--------------------------------------------------------------------------
        | Search Category
        |--------------------------------------------------------------------------
        */

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Latest First + Pagination
        |--------------------------------------------------------------------------
        */

        $categories = $query
            ->oldest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'categories.index',
            compact(
                'categories',
                'search'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE CATEGORY
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('categories.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE CATEGORY
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT CATEGORY
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view(
            'categories.edit',
            compact('category')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE CATEGORY
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE CATEGORY
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $category = Category::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Prevent deleting category containing products
        |--------------------------------------------------------------------------
        */

        if ($category->products()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Category cannot be deleted because products exist in this category.'
                );
        }

        $category->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Category deleted successfully.'
            );
    }
}