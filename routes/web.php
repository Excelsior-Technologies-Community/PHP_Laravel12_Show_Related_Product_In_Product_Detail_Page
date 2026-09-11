<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| CATEGORY ROUTES
|--------------------------------------------------------------------------
*/

Route::get(
    '/categories',
    [CategoryController::class, 'index']
)->name('categories.index');

Route::get(
    '/categories/create',
    [CategoryController::class, 'create']
)->name('categories.create');

Route::post(
    '/categories/store',
    [CategoryController::class, 'store']
)->name('categories.store');

Route::get(
    '/categories/edit/{id}',
    [CategoryController::class, 'edit']
)->name('categories.edit');

Route::post(
    '/categories/update/{id}',
    [CategoryController::class, 'update']
)->name('categories.update');

Route::get(
    '/categories/delete/{id}',
    [CategoryController::class, 'delete']
)->name('categories.delete');


/*
|--------------------------------------------------------------------------
| PRODUCT ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::get(
    '/product',
    [ProductController::class, 'index']
)->name('product.index');

Route::get(
    '/product/create',
    [ProductController::class, 'create']
)->name('product.create');

Route::post(
    '/product/store',
    [ProductController::class, 'store']
)->name('product.store');

Route::get(
    '/product/edit/{id}',
    [ProductController::class, 'edit']
)->name('product.edit');

Route::post(
    '/product/update/{id}',
    [ProductController::class, 'update']
)->name('product.update');


/*
|--------------------------------------------------------------------------
| PRODUCT TRASH
|--------------------------------------------------------------------------
*/

Route::get(
    '/product/trash',
    [ProductController::class, 'trash']
)->name('product.trash');

Route::get(
    '/product/restore/{id}',
    [ProductController::class, 'restore']
)->name('product.restore');

Route::post(
    '/product/bulk-delete',
    [ProductController::class, 'bulkDelete']
)->name('product.bulkDelete');

Route::get(
    '/product/export-csv',
    [ProductController::class, 'exportCsv']
)->name('product.exportCsv');

Route::get('/product/reviews', [ProductController::class, 'reviewModeration'])->name('product.reviews');
Route::post('/product/reviews/{id}/approve', [ProductController::class, 'approveReview'])->name('product.reviews.approve');
Route::post('/product/questions/{id}/approve', [ProductController::class, 'approveQuestion'])->name('product.questions.approve');
Route::post('/product/questions/{id}/answer', [ProductController::class, 'answerQuestion'])->name('product.questions.answer');

Route::get(
    '/product/delete/{id}',
    [ProductController::class, 'delete']
)->name('product.delete');


/*
|--------------------------------------------------------------------------
| FRONTEND
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [ProductController::class, 'frontendProducts']
)->name('frontend.products');

Route::get(
    '/product-detail/{id}',
    [ProductController::class, 'show']
)->name('frontend.product.detail');

Route::get(
    '/products/search-suggestions',
    [ProductController::class, 'ajaxSearch']
)->name('frontend.products.search');

Route::post(
    '/product-detail/{id}/review',
    [ProductController::class, 'storeReview']
)->name('product.review.store');

Route::post(
    '/product-detail/{id}/question',
    [ProductController::class, 'storeQuestion']
)->name('product.question.store');


/*
|--------------------------------------------------------------------------
| WISHLIST
|--------------------------------------------------------------------------
*/

Route::get(
    '/wishlist',
    [ProductController::class, 'wishlist']
)->name('wishlist');

Route::get('/compare', [ProductController::class, 'compare'])->name('compare');
Route::post('/compare/add/{id}', [ProductController::class, 'addToCompare'])->name('compare.add');
Route::post('/compare/remove/{id}', [ProductController::class, 'removeFromCompare'])->name('compare.remove');

Route::post(
    '/wishlist/add/{id}',
    [ProductController::class, 'addToWishlist']
)->name('wishlist.add');

Route::post(
    '/wishlist/remove/{id}',
    [ProductController::class, 'removeFromWishlist']
)->name('wishlist.remove');