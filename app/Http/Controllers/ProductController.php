<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Show admin product list
    public function index()
    {
        // Fetch all products with category (old first, new last)
        $products = Product::with('category')
            ->orderBy('id', 'ASC')
            ->get();

        return view('product.index', compact('products'));
    }

    // Show add product form
    public function create()
    {
        // Get all categories for dropdown
        $categories = Category::all();

        return view('product.create', compact('categories'));
    }

    // Store new product
    public function store(Request $request)
    {
        // Validate product input
        $request->validate([
            'name'        => 'required',
            'price'       => 'required',
            'category_id' => 'required',
            'image'       => 'image|mimes:jpg,png,jpeg,webp'
        ]);

        // Default image value
        $imageName = null;

        // Upload product image if exists
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('products'), $imageName);
        }

        // Save product to database
        Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'details'     => $request->details,
            'category_id' => $request->category_id,
            'image'       => $imageName
        ]);

        return redirect()->route('product.index');
    }

    // Show edit product form
    public function edit($id)
    {
        // Get product by id
        $product = Product::findOrFail($id);

        // Get categories for dropdown
        $categories = Category::all();

        return view('product.edit', compact('product', 'categories'));
    }

    // Update product details
    public function update(Request $request, $id)
    {
        // Get product by id
        $product = Product::findOrFail($id);

        // Keep old image by default
        $imageName = $product->image;

        // Upload new image if selected
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('products'), $imageName);
        }

        // Update product data
        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'details'     => $request->details,
            'category_id' => $request->category_id,
            'image'       => $imageName
        ]);

        return redirect()->route('product.index');
    }

    // Delete product
    public function delete($id)
    {
        // Remove product by id
        Product::findOrFail($id)->delete();

        return redirect()->back();
    }

    // Show frontend product detail page
    public function show($id)
    {
        // Get product with category
        $product = Product::with('category')->findOrFail($id);

        // Fetch related products from same category (excluding current)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->get();

        return view('frontend.product-detail', compact('product', 'relatedProducts'));
    }

    // Show frontend product listing
    public function frontendProducts()
    {
        // Fetch products for frontend (old first, new last)
        $products = Product::with('category')
            ->orderBy('id', 'ASC')
            ->get();

        return view('frontend.products', compact('products'));
    }
}
