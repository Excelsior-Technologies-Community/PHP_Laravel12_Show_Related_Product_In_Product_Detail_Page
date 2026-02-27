# PHP_Laravel12_Show_Related_Product_In_Product_Detail_Page

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/Blade-Views-orange?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql">
  <img src="https://img.shields.io/badge/Related%20Products-Category%20Based-success?style=for-the-badge">
  <img src="https://img.shields.io/badge/Admin%20Panel-CRUD-blue?style=for-the-badge">
</p>


---

##  Overview

This project is a **Laravel 12 based Product Management System** that includes both **Admin Panel** and **Frontend UI**.

It demonstrates how to:
- Manage **Categories & Products** from Admin panel
- Upload and display product images
- Show **Product Detail Page** on frontend
- Display **Related Products** based on the same category
- Use proper **Eloquent Relationships**
- Build real-world CRUD functionality with clean UI

This project is ideal for:
- Laravel beginners & intermediates  
- Understanding One-to-Many relationships  
- Admin + Frontend flow  
- Interview & portfolio projects  

---

##  Features

- Laravel 12
- Category CRUD (Admin)
- Product CRUD (Admin)
- Image Upload
- Frontend Product Listing
- Product Detail Page
- Related Products by Category
- Blade Views (No CSS)
- MySQL Database

---

##  Folder Structure

```text
laravel-shop/
│
├── app/
│   ├── Models/
│   │   ├── Category.php
│   │   └── Product.php
│   └── Http/
│       └── Controllers/
│           ├── CategoryController.php
│           └── ProductController.php
│
├── database/
│   └── migrations/
│       ├── create_categories_table.php
│       └── create_products_table.php
│
├── resources/
│   └── views/
│       ├── layout/app.blade.php
│       ├── categories/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── product/
│       │   ├── index.blade.php
│       │   └── create.blade.php
│       └── frontend/
│           ├── products.blade.php
│           └── product-detail.blade.php
│
├── public/products/
├── routes/web.php
└── README.md
```

---

##  STEP 1: Installation

```bash
composer create-project laravel/laravel laravel-shop
```

---

##  STEP 2: Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prod_crud
DB_USERNAME=root
DB_PASSWORD=
```

---

##  STEP 3: Create Models & Migrations

```bash
php artisan make:model Category -m
php artisan make:model Product -m
```

### Categories Table

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});
```

### Products Table

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->text('details')->nullable();
    $table->string('image')->nullable();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

```bash
php artisan migrate
```

---

##  STEP 4: Models

### Category.php

```php
class Category extends Model
{
    protected $fillable = ['name','slug'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

### Product.php

```php
class Product extends Model
{
    protected $fillable = [
        'name','price','details','image','category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

---

##  STEP 5: Create Controllers

```bash
php artisan make:controller CategoryController
php artisan make:controller ProductController  
```

### CategoryController.php

```php
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        Category::create([
            'name'=>$request->name,
            'slug'=>Str::slug($request->name)
        ]);
        return redirect()->route('categories.index');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request,$id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name'=>$request->name,
            'slug'=>Str::slug($request->name)
        ]);
        return redirect()->route('categories.index');
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        return back();
    }
}
```

---

### ProductController.php

```php
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $imageName = null;

        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('products'),$imageName);
        }

        Product::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'details'=>$request->details,
            'category_id'=>$request->category_id,
            'image'=>$imageName
        ]);

        return redirect()->route('product.index');
    }

    public function frontendProducts()
    {
        $products = Product::with('category')->get();
        return view('frontend.products', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        $relatedProducts = Product::where('category_id',$product->category_id)
            ->where('id','!=',$product->id)
            ->get();

        return view('frontend.product-detail', compact('product','relatedProducts'));
    }
}
```

---

##  STEP 6: Routes (web.php)

```php
Route::get('/categories', [CategoryController::class,'index'])->name('categories.index');
Route::get('/categories/create', [CategoryController::class,'create'])->name('categories.create');
Route::post('/categories/store', [CategoryController::class,'store'])->name('categories.store');
Route::get('/categories/edit/{id}', [CategoryController::class,'edit'])->name('categories.edit');
Route::post('/categories/update/{id}', [CategoryController::class,'update'])->name('categories.update');
Route::get('/categories/delete/{id}', [CategoryController::class,'delete'])->name('categories.delete');

Route::get('/product', [ProductController::class,'index'])->name('product.index');
Route::get('/product/create', [ProductController::class,'create'])->name('product.create');
Route::post('/product/store', [ProductController::class,'store'])->name('product.store');

Route::get('/', [ProductController::class,'frontendProducts'])->name('frontend.products');
Route::get('/product-detail/{id}', [ProductController::class,'show'])->name('frontend.product.detail');
```

---

##  STEP 7 Blade Files –

---

###  resources/views/layout/app.blade.php

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Shop</title>
</head>
<body>

<h2>Laravel Product Management</h2>
<hr>

@yield('content')

</body>
</html>
```

---

##  CATEGORY (Admin)

---

###  resources/views/categories/index.blade.php

```blade
@extends('layout.app')

@section('content')

<a href="{{ route('categories.create') }}">Add Category</a>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Action</th>
    </tr>

    @foreach($categories as $cat)
    <tr>
        <td>{{ $cat->id }}</td>
        <td>{{ $cat->name }}</td>
        <td>
            <a href="{{ route('categories.edit',$cat->id) }}">Edit</a>
            <a href="{{ route('categories.delete',$cat->id) }}">Delete</a>
        </td>
    </tr>
    @endforeach
</table>

@endsection
```

---

###  resources/views/categories/create.blade.php

```blade
@extends('layout.app')

@section('content')

<form method="POST" action="{{ route('categories.store') }}">
    @csrf
    <input name="name" placeholder="Category Name" required>
    <button>Save</button>
</form>

@endsection
```

---

###  resources/views/categories/edit.blade.php

```blade
@extends('layout.app')

@section('content')

<form method="POST" action="{{ route('categories.update',$category->id) }}">
    @csrf
    <input name="name" value="{{ $category->name }}" required>
    <button>Update</button>
</form>

@endsection
```

---

##  PRODUCT (Admin)

---

###  resources/views/product/index.blade.php

```blade
@extends('layout.app')

@section('content')

<a href="{{ route('product.create') }}">Add Product</a>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Category</th>
    <th>Price</th>
    <th>Image</th>
</tr>

@foreach($products as $p)
<tr>
    <td>{{ $p->id }}</td>
    <td>{{ $p->name }}</td>
    <td>{{ $p->category->name }}</td>
    <td>{{ $p->price }}</td>
    <td>
        @if($p->image)
            <img src="{{ asset('products/'.$p->image) }}" width="60">
        @endif
    </td>
</tr>
@endforeach

</table>

@endsection
```

---

###  resources/views/product/create.blade.php

```blade
@extends('layout.app')

@section('content')

<form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
@csrf

<input name="name" placeholder="Product Name" required><br><br>

<input name="price" placeholder="Price" required><br><br>

<textarea name="details" placeholder="Details"></textarea><br><br>

<select name="category_id">
@foreach($categories as $cat)
<option value="{{ $cat->id }}">{{ $cat->name }}</option>
@endforeach
</select><br><br>

<input type="file" name="image"><br><br>

<button>Save Product</button>

</form>

@endsection
```

---

##  FRONTEND

---

###  resources/views/frontend/products.blade.php

```blade
@extends('layout.app')

@section('content')

<h3>All Products</h3>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Price</th>
    <th>Image</th>
    <th>Action</th>
</tr>

@foreach($products as $p)
<tr>
    <td>{{ $p->name }}</td>
    <td>{{ $p->price }}</td>
    <td>
        <img src="{{ asset('products/'.$p->image) }}" width="80">
    </td>
    <td>
        <a href="{{ route('frontend.product.detail',$p->id) }}">View</a>
    </td>
</tr>
@endforeach

</table>

@endsection
```

---

###  resources/views/frontend/product-detail.blade.php

```blade
@extends('layout.app')

@section('content')

<h2>{{ $product->name }}</h2>

<p>Category: {{ $product->category->name }}</p>
<p>Price: {{ $product->price }}</p>
<p>{{ $product->details }}</p>

@if($product->image)
<img src="{{ asset('products/'.$product->image) }}" width="200">
@endif

<hr>

<h3>Related Products</h3>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Price</th>
    <th>Image</th>
</tr>

@foreach($relatedProducts as $rp)
<tr>
    <td>{{ $rp->name }}</td>
    <td>{{ $rp->price }}</td>
    <td>
        <img src="{{ asset('products/'.$rp->image) }}" width="80">
    </td>
</tr>
@endforeach
</table>

@endsection
```

---

##  Output Screenshots
---

###  Admin Panel – Category Management
INDEX PAGE:-

<img width="1311" height="555" alt="Screenshot 2025-12-24 132658" src="https://github.com/user-attachments/assets/28d587f0-7e8e-456d-9a3e-5e716208735a" />

CREATE PAGE:-

<img width="1346" height="303" alt="Screenshot 2025-12-24 132706" src="https://github.com/user-attachments/assets/f4f03be7-a595-47a3-b795-a169dd90c2b8" />

EDIT PAGE:-
<img width="1294" height="306" alt="Screenshot 2025-12-24 132717" src="https://github.com/user-attachments/assets/813ede88-3b08-4778-8557-187c4480473b" />

---

###  Admin Panel – Product Management

INDEX PAGE:-

<img width="1301" height="808" alt="Screenshot 2025-12-24 133327" src="https://github.com/user-attachments/assets/d5dbb161-315a-4c88-ae08-774a373b9b8c" />

CREATE PAGE:-

<img width="1324" height="754" alt="Screenshot 2025-12-24 133336" src="https://github.com/user-attachments/assets/cbe9508c-329d-4ec4-af66-054b86e6be70" />

EDIT PAGE:-

<img width="1279" height="823" alt="Screenshot 2025-12-24 133346" src="https://github.com/user-attachments/assets/5692bedc-c6e4-4a1d-86c9-765ee67146ec" />


---

###  Frontend – All Products Page

PRODUCTS PAGE:-

<img width="1091" height="740" alt="Screenshot 2025-12-24 133627" src="https://github.com/user-attachments/assets/cc2fba89-acfe-48de-a4cb-c5ec0352af31" />

---

###  Frontend – Product Detail Page & Related Products Section
DETAIL & RELATED PRODUCTS PAGE:-

<img width="1005" height="813" alt="Screenshot 2025-12-24 133637" src="https://github.com/user-attachments/assets/a33fe30f-5b55-46b2-bf1c-cee85a4e817f" />


