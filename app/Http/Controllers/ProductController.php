<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN PRODUCT LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->input('search');

        $categoryId = $request->input('category_id');

        $priceRange = $request->input('price_range');

        $sort = $request->input('sort', 'latest');

        $status = $request->input('status');

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
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('details', 'like', '%' . $search . '%');
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

            $query->where('price', '<', 500);

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

            $query->where('price', '>', 2000);
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($status) {
            $query->where('status', $status);
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

            case 'oldest':

                $query->oldest();

                break;

            case 'latest':

            default:

                $query->latest();

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION - NEW FEATURE
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate(5)
            ->withQueryString();

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
                'sort',
                'status'
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

            'status' =>
                'required|in:active,inactive',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

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

            'status' =>
                $request->status,
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

            'status' =>
                'required|in:active,inactive',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        $imageName =
            $product->image;

        if ($request->hasFile('image')) {

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

            'status' =>
                $request->status,
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
    | SOFT DELETE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $product =
            Product::findOrFail($id);

        $product->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Product moved to trash.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | TRASH
    |--------------------------------------------------------------------------
    */

    public function trash()
    {
        $products =
            Product::onlyTrashed()
                ->with('category')
                ->latest('deleted_at')
                ->paginate(5);

        return view(
            'product.trash',
            compact('products')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function restore($id)
    {
        $product =
            Product::onlyTrashed()
                ->findOrFail($id);

        $product->restore();

        return redirect()
            ->back()
            ->with(
                'success',
                'Product restored successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        Product::whereIn(
            'id',
            $request->product_ids
        )->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                count($request->product_ids)
                . ' product(s) moved to trash.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CSV EXPORT
    |--------------------------------------------------------------------------
    */

    public function exportCsv(): StreamedResponse
    {
        $products =
            Product::with('category')
                ->latest()
                ->get();

        $fileName =
            'products_' .
            date('Y-m-d_H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($products) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                fputcsv(
                    $handle,
                    [
                        'ID',
                        'Name',
                        'Category',
                        'Price',
                        'Status',
                        'Details',
                        'Created At'
                    ]
                );

                foreach ($products as $product) {

                    fputcsv(
                        $handle,
                        [
                            $product->id,
                            $product->name,
                            $product->category?->name ?? 'No Category',
                            $product->price,
                            $product->status,
                            $product->details,
                            $product->created_at,
                        ]
                    );
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAIL
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $product =
            Product::with('category')
                ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Related Products
        |--------------------------------------------------------------------------
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
                ->where(
                    'status',
                    'active'
                )
                ->latest()
                ->limit(6)
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Recently Viewed
        |--------------------------------------------------------------------------
        */

        $recentlyViewed =
            session()->get(
                'recently_viewed_products',
                []
            );

        $recentlyViewed =
            array_values(
                array_diff(
                    $recentlyViewed,
                    [$product->id]
                )
            );

        array_unshift(
            $recentlyViewed,
            $product->id
        );

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
                    ->where(
                        'status',
                        'active'
                    )
                    ->get()
                    ->keyBy('id');

            foreach ($recentIds as $recentId) {

                if (
                    $recentProducts->has(
                        $recentId
                    )
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
        | Wishlist Status
        |--------------------------------------------------------------------------
        */

        $wishlist =
            session()->get(
                'wishlist',
                []
            );

        $isWishlisted =
            in_array(
                $product->id,
                $wishlist
            );

        return view(
            'frontend.product-detail',
            compact(
                'product',
                'relatedProducts',
                'recentlyViewedProducts',
                'isWishlisted'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FRONTEND PRODUCTS
    |--------------------------------------------------------------------------
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

        $minPrice =
            $request->input('min_price');

        $maxPrice =
            $request->input('max_price');

        $sort =
            $request->input(
                'sort',
                'latest'
            );

        $query =
            Product::with('category')
                ->where(
                    'status',
                    'active'
                );

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
        | Category
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
        | Existing Price Range
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
        | NEW: Custom Min Price
        |--------------------------------------------------------------------------
        */

        if (
            $minPrice !== null &&
            $minPrice !== ''
        ) {

            $query->where(
                'price',
                '>=',
                $minPrice
            );
        }

        /*
        |--------------------------------------------------------------------------
        | NEW: Custom Max Price
        |--------------------------------------------------------------------------
        */

        if (
            $maxPrice !== null &&
            $maxPrice !== ''
        ) {

            $query->where(
                'price',
                '<=',
                $maxPrice
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

            case 'oldest':

                $query->oldest();

                break;

            default:

                $query->latest();

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $products =
            $query
                ->paginate(8)
                ->withQueryString();

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
                'minPrice',
                'maxPrice',
                'sort'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADD TO WISHLIST
    |--------------------------------------------------------------------------
    */

    public function addToWishlist($id)
    {
        $product =
            Product::where(
                'status',
                'active'
            )->findOrFail($id);

        $wishlist =
            session()->get(
                'wishlist',
                []
            );

        if (!in_array(
            $product->id,
            $wishlist
        )) {

            $wishlist[] =
                $product->id;
        }

        session()->put(
            'wishlist',
            $wishlist
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Product added to wishlist.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE FROM WISHLIST
    |--------------------------------------------------------------------------
    */

    public function removeFromWishlist($id)
    {
        $wishlist =
            session()->get(
                'wishlist',
                []
            );

        $wishlist =
            array_values(
                array_diff(
                    $wishlist,
                    [$id]
                )
            );

        session()->put(
            'wishlist',
            $wishlist
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Product removed from wishlist.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | WISHLIST
    |--------------------------------------------------------------------------
    */

    public function wishlist()
    {
        $ids =
            session()->get(
                'wishlist',
                []
            );

        $products =
            Product::with('category')
                ->whereIn(
                    'id',
                    $ids
                )
                ->where(
                    'status',
                    'active'
                )
                ->get();

        return view(
            'frontend.wishlist',
            compact('products')
        );
    }
}