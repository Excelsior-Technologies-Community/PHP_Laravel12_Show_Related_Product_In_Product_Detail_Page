<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN PRODUCT LISTING
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->input('search');

        $categoryId = $request->input('category_id');

        $priceRange = $request->input('price_range');

        $sort = $request->input('sort', 'latest');


        /*
        |--------------------------------------------------------------------------
        | Product Query
        |--------------------------------------------------------------------------
        */

        $query = Product::with('category');


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'details',
                    'like',
                    '%' . $search . '%'
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

        if ($categoryId) {

            $query->where(
                'category_id',
                $categoryId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Price Filter
        |--------------------------------------------------------------------------
        */

        if ($priceRange === 'under_500') {

            $query->where(
                'price',
                '<',
                500
            );

        } elseif ($priceRange === '500_1000') {

            $query->whereBetween(
                'price',
                [500, 1000]
            );

        } elseif ($priceRange === '1000_2000') {

            $query->whereBetween(
                'price',
                [1000, 2000]
            );

        } elseif ($priceRange === 'above_2000') {

            $query->where(
                'price',
                '>',
                2000
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($sort) {

            case 'price_low':

                $query->orderBy(
                    'price',
                    'asc'
                );

                break;


            case 'price_high':

                $query->orderBy(
                    'price',
                    'desc'
                );

                break;


            case 'name_asc':

                $query->orderBy(
                    'name',
                    'asc'
                );

                break;


            case 'name_desc':

                $query->orderBy(
                    'name',
                    'desc'
                );

                break;


            case 'latest':

            default:

                $query->latest();

                break;

        }


        /*
        |--------------------------------------------------------------------------
        | Get Products
        |--------------------------------------------------------------------------
        */

        $products = $query->get();


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = Category::orderBy(
            'name',
            'asc'
        )->get();


        return view(
            'product.index',
            compact(
                'products',
                'categories',
                'search',
                'categoryId',
                'priceRange',
                'sort'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::all();

        return view(
            'product.create',
            compact('categories')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name' =>
                'required|string|max:255',

            'price' =>
                'required|numeric|min:0',

            'category_id' =>
                'required|exists:categories,id',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        $imageName = null;

        if ($request->hasFile('image')) {

            $imageName =
                time()
                . '_'
                . Str::random(5)
                . '.'
                . $request->image->extension();

            $request->image->move(
                public_path('products'),
                $imageName
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */

        Product::create([

            'name' =>
                $request->name,

            'slug' =>
                Str::slug($request->name),

            'price' =>
                $request->price,

            'details' =>
                $request->details,

            'category_id' =>
                $request->category_id,

            'image' =>
                $imageName,

        ]);


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                'Product created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT PRODUCT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $product =
            Product::findOrFail($id);

        $categories =
            Category::all();

        return view(
            'product.edit',
            compact(
                'product',
                'categories'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $product =
            Product::findOrFail($id);


        $request->validate([

            'name' =>
                'required|string|max:255',

            'price' =>
                'required|numeric|min:0',

            'category_id' =>
                'required|exists:categories,id',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Existing Image
        |--------------------------------------------------------------------------
        */

        $imageName =
            $product->image;


        /*
        |--------------------------------------------------------------------------
        | New Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            /*
            | Delete old image
            */

            if (
                $product->image &&
                file_exists(
                    public_path(
                        'products/' .
                        $product->image
                    )
                )
            ) {

                unlink(
                    public_path(
                        'products/' .
                        $product->image
                    )
                );
            }


            /*
            | Upload new image
            */

            $imageName =
                time()
                . '_'
                . Str::random(5)
                . '.'
                . $request->image->extension();

            $request->image->move(
                public_path('products'),
                $imageName
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */

        $product->update([

            'name' =>
                $request->name,

            'slug' =>
                Str::slug($request->name),

            'price' =>
                $request->price,

            'details' =>
                $request->details,

            'category_id' =>
                $request->category_id,

            'image' =>
                $imageName,

        ]);


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $product =
            Product::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Delete Image
        |--------------------------------------------------------------------------
        */

        if (
            $product->image &&
            file_exists(
                public_path(
                    'products/' .
                    $product->image
                )
            )
        ) {

            unlink(
                public_path(
                    'products/' .
                    $product->image
                )
            );
        }


        $product->delete();


        return redirect()
            ->back()
            ->with(
                'success',
                'Product deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAIL
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Current Product
        |--------------------------------------------------------------------------
        */

        $product =
            Product::with('category')
                ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | FEATURE 1:
        | Related Products
        |--------------------------------------------------------------------------
        |
        | Automatically show products from
        | the same category.
        |
        */

        $relatedProducts =
            Product::with('category')
                ->where(
                    'category_id',
                    $product->category_id
                )
                ->where(
                    'id',
                    '!=',
                    $product->id
                )
                ->latest()
                ->limit(6)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | FEATURE 2:
        | Recently Viewed Products
        |--------------------------------------------------------------------------
        */

        $recentlyViewed =
            session()->get(
                'recently_viewed_products',
                []
            );


        /*
        | Remove current product
        */

        $recentlyViewed =
            array_values(
                array_diff(
                    $recentlyViewed,
                    [$product->id]
                )
            );


        /*
        | Add current product at beginning
        */

        array_unshift(
            $recentlyViewed,
            $product->id
        );


        /*
        | Keep latest 6
        */

        $recentlyViewed =
            array_slice(
                $recentlyViewed,
                0,
                6
            );


        session()->put(
            'recently_viewed_products',
            $recentlyViewed
        );


        /*
        |--------------------------------------------------------------------------
        | Get Recently Viewed Products
        |--------------------------------------------------------------------------
        */

        $recentIds =
            array_slice(
                $recentlyViewed,
                1
            );


        $recentlyViewedProducts =
            collect();


        if (!empty($recentIds)) {

            $recentProducts =
                Product::with('category')
                    ->whereIn(
                        'id',
                        $recentIds
                    )
                    ->get()
                    ->keyBy('id');


            /*
            |--------------------------------------------------------------------------
            | Preserve Session Order
            |--------------------------------------------------------------------------
            */

            foreach ($recentIds as $recentId) {

                if (
                    $recentProducts
                        ->has($recentId)
                ) {

                    $recentlyViewedProducts->push(
                        $recentProducts->get(
                            $recentId
                        )
                    );
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Product Detail View
        |--------------------------------------------------------------------------
        */

        return view(
            'frontend.product-detail',
            compact(
                'product',
                'relatedProducts',
                'recentlyViewedProducts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FRONTEND PRODUCTS
    |--------------------------------------------------------------------------
    |
    | Search + Category Filter +
    | Price Filter + Sorting
    |
    */

    public function frontendProducts(
        Request $request
    ) {

        $search =
            $request->input('search');

        $categoryId =
            $request->input('category_id');

        $priceRange =
            $request->input('price_range');

        $sort =
            $request->input(
                'sort',
                'latest'
            );


        /*
        |--------------------------------------------------------------------------
        | Product Query
        |--------------------------------------------------------------------------
        */

        $query =
            Product::with('category');


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'details',
                    'like',
                    '%' . $search . '%'
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

        if ($categoryId) {

            $query->where(
                'category_id',
                $categoryId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Price Filter
        |--------------------------------------------------------------------------
        */

        if ($priceRange === 'under_500') {

            $query->where(
                'price',
                '<',
                500
            );

        } elseif ($priceRange === '500_1000') {

            $query->whereBetween(
                'price',
                [500, 1000]
            );

        } elseif ($priceRange === '1000_2000') {

            $query->whereBetween(
                'price',
                [1000, 2000]
            );

        } elseif ($priceRange === 'above_2000') {

            $query->where(
                'price',
                '>',
                2000
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($sort) {

            case 'price_low':

                $query->orderBy(
                    'price',
                    'asc'
                );

                break;


            case 'price_high':

                $query->orderBy(
                    'price',
                    'desc'
                );

                break;


            case 'name_asc':

                $query->orderBy(
                    'name',
                    'asc'
                );

                break;


            case 'name_desc':

                $query->orderBy(
                    'name',
                    'desc'
                );

                break;


            case 'latest':

            default:

                $query->latest();

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products =
            $query->get();


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories =
            Category::orderBy(
                'name',
                'asc'
            )->get();


        return view(
            'frontend.products',
            compact(
                'products',
                'categories',
                'search',
                'categoryId',
                'priceRange',
                'sort'
            )
        );
    }
}