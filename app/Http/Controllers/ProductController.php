<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Wishlist;
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
                                    ->orWhere('details', 'like', '%' . $search . '%')
                                    ->orWhere('sku', 'like', '%' . $search . '%')
                                    ->orWhere('brand', 'like', '%' . $search . '%');
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

            case 'best_selling':
                $query->orderByDesc('sales_count');
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

            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'brand' => 'nullable|string|max:100',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'tags' => 'nullable|string|max:1000',
            'featured' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'variant_names' => 'nullable|array',
            'variant_names.*' => 'nullable|string|max:100',
            'variant_values' => 'nullable|array',
            'variant_values.*' => 'nullable|string|max:100',
            'variant_stocks' => 'nullable|array',
            'variant_stocks.*' => 'nullable|integer|min:0',

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

        $product = Product::create([

            'name' =>
                $request->name,

            'slug' =>
                Str::slug($request->name),

            'price' =>
                $request->price,

            'sku' => $request->sku ?: null,
            'brand' => $request->brand,
            'discount_price' => $request->discount_price ?: null,
            'stock' => $request->stock,

            'details' =>
                $request->details,

            'category_id' =>
                $request->category_id,

            'image' =>
                $imageName,

            'status' =>
                $request->status,

            'tags' => $this->parseTags($request->tags),
            'featured' => $request->boolean('featured'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
        ]);

        $this->saveAdditionalProductData($product, $request);

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
            Product::with(['images', 'variants'])->findOrFail($id);

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

            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'brand' => 'nullable|string|max:100',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'tags' => 'nullable|string|max:1000',
            'featured' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'variant_names' => 'nullable|array',
            'variant_names.*' => 'nullable|string|max:100',
            'variant_values' => 'nullable|array',
            'variant_values.*' => 'nullable|string|max:100',
            'variant_stocks' => 'nullable|array',
            'variant_stocks.*' => 'nullable|integer|min:0',

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

            'sku' => $request->sku ?: null,
            'brand' => $request->brand,
            'discount_price' => $request->discount_price ?: null,
            'stock' => $request->stock,

            'details' =>
                $request->details,

            'category_id' =>
                $request->category_id,

            'image' =>
                $imageName,

            'status' =>
                $request->status,

            'tags' => $this->parseTags($request->tags),
            'featured' => $request->boolean('featured'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
        ]);

        $this->saveAdditionalProductData($product, $request, true);

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
            Product::with(['category', 'images', 'variants', 'reviews', 'questions'])
                ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Related Products
        |--------------------------------------------------------------------------
        */

        $relatedProducts =
            Product::with('category')
                ->where(function ($query) use ($product) {
                    $query->where('category_id', $product->category_id)
                        ->orWhere('brand', $product->brand);
                    foreach ($product->tag_list as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                })
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

        $isWishlisted = Wishlist::where('product_id', $product->id)
            ->where('session_id', session()->getId())
            ->exists();

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

    public function ajaxSearch(Request $request)
    {
        $term = trim((string) $request->input('q'));

        if ($term === '') {
            return response()->json([]);
        }

        return response()->json(
            Product::where('status', 'active')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('brand', 'like', "%{$term}%");
                })
                ->limit(8)
                ->get(['id', 'name', 'price', 'discount_price'])
        );
    }

    public function storeReview(Request $request, $id)
    {
        $product = Product::where('status', 'active')->findOrFail($id);
        $data = $request->validate([
            'customer_name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:2000',
        ]);
        $data['product_id'] = $product->id;
        $data['is_approved'] = false;
        $data['is_verified_purchase'] = false;
        ProductReview::create($data);

        return back()->with('success', 'Review submitted for approval.');
    }

    public function storeQuestion(Request $request, $id)
    {
        $product = Product::where('status', 'active')->findOrFail($id);
        $data = $request->validate([
            'customer_name' => 'required|string|max:100',
            'question' => 'required|string|max:2000',
        ]);
        $data['product_id'] = $product->id;
        $data['is_approved'] = false;
        ProductQuestion::create($data);

        return back()->with('success', 'Question submitted for approval.');
    }

    public function reviewModeration()
    {
        $reviews = ProductReview::with('product')->latest()->paginate(20);
        $questions = ProductQuestion::with('product')->latest()->paginate(20, ['*'], 'questions');

        return view('product.reviews', compact('reviews', 'questions'));
    }

    public function approveReview($id)
    {
        ProductReview::findOrFail($id)->update(['is_approved' => true]);

        return back()->with('success', 'Review approved.');
    }

    public function approveQuestion($id)
    {
        ProductQuestion::findOrFail($id)->update(['is_approved' => true]);

        return back()->with('success', 'Question approved.');
    }

    public function answerQuestion(Request $request, $id)
    {
        $data = $request->validate(['answer' => 'required|string|max:2000']);
        ProductQuestion::findOrFail($id)->update($data + ['is_approved' => true]);

        return back()->with('success', 'Question answered.');
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

            case 'best_selling':
                $query->orderByDesc('sales_count');
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

        $wishlistIds = Wishlist::where('session_id', session()->getId())
            ->pluck('product_id')
            ->all();

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
                'sort',
                'wishlistIds'
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

        Wishlist::firstOrCreate([
            'product_id' => $product->id,
            'session_id' => session()->getId(),
        ]);

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
        Wishlist::where('product_id', $id)
            ->where('session_id', session()->getId())
            ->delete();

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
        $products = Product::with('category')
            ->whereHas('wishlistItems', function ($query) {
                $query->where('session_id', session()->getId());
            })
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

    public function addToCompare($id)
    {
        Product::where('status', 'active')->findOrFail($id);
        $compare = session()->get('compare', []);
        if (!in_array((int) $id, $compare) && count($compare) < 4) {
            $compare[] = (int) $id;
        }
        session()->put('compare', $compare);

        return back()->with('success', 'Product added to compare.');
    }

    public function removeFromCompare($id)
    {
        session()->put('compare', array_values(array_diff(session()->get('compare', []), [(int) $id])));

        return back()->with('success', 'Product removed from compare.');
    }

    public function compare()
    {
        $products = Product::with('category')->whereIn('id', session()->get('compare', []))->get();

        return view('frontend.compare', compact('products'));
    }

    private function parseTags(?string $tags): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $tags))));
    }

    private function saveAdditionalProductData(Product $product, Request $request, bool $replaceVariants = false): void
    {
        if ($replaceVariants) {
            $product->variants()->delete();
        }

        foreach ($request->input('variant_names', []) as $index => $name) {
            $value = $request->input("variant_values.{$index}");
            if (trim((string) $name) !== '' && trim((string) $value) !== '') {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => trim($name),
                    'value' => trim($value),
                    'stock' => (int) $request->input("variant_stocks.{$index}", 0),
                ]);
            }
        }

        foreach ($request->file('images', []) as $index => $image) {
            $path = time() . '_' . Str::random(8) . '.' . $image->extension();
            $image->move(public_path('products'), $path);
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'sort_order' => $index,
            ]);
        }
    }
}