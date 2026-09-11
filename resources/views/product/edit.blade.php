@extends('layout.app')

@section('content')

<div class="card-wrapper">

    {{-- ============================= --}}
    {{-- PAGE HEADER --}}
    {{-- ============================= --}}

    <div class="page-header">

        <h2>Edit Product</h2>

        <a href="{{ route('product.index') }}">
            <button
                type="button"
                class="btn btn-light"
            >
                ← Back to Products
            </button>
        </a>

    </div>


    {{-- ============================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ============================= --}}

    @if($errors->any())

        <div class="alert error">

            <strong>Please fix the following errors:</strong>

            <ul style="margin: 10px 0 0 20px;">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ============================= --}}
    {{-- EDIT PRODUCT FORM --}}
    {{-- ============================= --}}

    <form
        action="{{ route('product.update', $product->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="product-form"
    >

        @csrf


        {{-- ============================= --}}
        {{-- PRODUCT NAME --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label for="name">
                Product Name
                <span class="required">*</span>
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $product->name) }}"
                placeholder="Enter product name"
                required
            >

            @error('name')
                <small class="error-text">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- ============================= --}}
        {{-- CATEGORY --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label for="category_id">
                Category
                <span class="required">*</span>
            </label>

            <select
                id="category_id"
                name="category_id"
                required
            >

                <option value="">
                    Select Category
                </option>

                @foreach($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        {{ (string)old('category_id', $product->category_id) === (string)$category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>

            @error('category_id')
                <small class="error-text">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- ============================= --}}
        {{-- PRICE --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label for="price">
                Price
                <span class="required">*</span>
            </label>

            <input
                type="number"
                id="price"
                name="price"
                value="{{ old('price', $product->price) }}"
                placeholder="Enter product price"
                min="0"
                step="0.01"
                required
            >

            @error('price')
                <small class="error-text">
                    {{ $message }}
                </small>
            @enderror

        </div>

            <div class="form-grid">
                <div class="form-group"><label for="sku">SKU / Product Code</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}"></div>
                <div class="form-group"><label for="brand">Brand</label><input id="brand" name="brand" value="{{ old('brand', $product->brand) }}"></div>
                <div class="form-group"><label for="discount_price">Sale / Discount Price</label><input type="number" step="0.01" min="0" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}"></div>
                <div class="form-group"><label for="stock">Stock</label><input type="number" min="0" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required></div>
            </div>

            <div class="form-group"><label for="tags">Tags <small>(comma separated)</small></label><input id="tags" name="tags" value="{{ old('tags', implode(', ', $product->tags ?? [])) }}"></div>


        {{-- ============================= --}}
        {{-- STATUS --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label for="status">
                Product Status
                <span class="required">*</span>
            </label>

            <select
                id="status"
                name="status"
                required
            >

                <option
                    value="active"
                    {{ old('status', $product->status) === 'active' ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="inactive"
                    {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}
                >
                    Inactive
                </option>

            </select>

            @error('status')
                <small class="error-text">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- ============================= --}}
        {{-- PRODUCT DETAILS --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label for="details">
                Product Details
            </label>

            <textarea
                id="details"
                name="details"
                rows="6"
                placeholder="Enter product details"
            >{{ old('details', $product->details) }}</textarea>

            @error('details')
                <small class="error-text">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- ============================= --}}
        {{-- CURRENT IMAGE --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label>
                Current Product Image
            </label>

            @if($product->image)

                <div class="current-image-box">

                    <img
                        src="{{ asset('products/' . $product->image) }}"
                        alt="{{ $product->name }}"
                        class="current-product-image"
                    >

                    <div class="current-image-name">
                        {{ $product->image }}
                    </div>

                </div>

            @else

                <div class="no-current-image">
                    No image available
                </div>

            @endif

        </div>


        {{-- ============================= --}}
        {{-- NEW IMAGE --}}
        {{-- ============================= --}}

        <div class="form-group">

            <label for="image">
                Replace Product Image
            </label>

            <input
                type="file"
                id="image"
                name="image"
                accept="image/jpeg,image/png,image/jpg,image/webp"
            >

            <small class="help-text">
                Leave empty if you want to keep the current image.
            </small>

            @error('image')
                <small class="error-text">
                    {{ $message }}
                </small>
            @enderror


            {{-- New Image Preview --}}
            <div
                id="imagePreviewContainer"
                class="image-preview-container"
                style="display: none;"
            >

                <p>
                    New Image Preview
                </p>

                <img
                    id="imagePreview"
                    src=""
                    alt="New Image Preview"
                    class="image-preview"
                >

            </div>

        </div>

            <div class="form-group"><label>Additional Product Images</label><input type="file" name="images[]" accept="image/*" multiple></div>

            <div class="form-group"><label>Variants</label><div class="variant-grid"><strong>Type</strong><strong>Value</strong><strong>Stock</strong>@for($index = 0; $index < 4; $index++)@php $variant = $product->variants[$index] ?? null; @endphp<input name="variant_names[]" value="{{ old("variant_names.{$index}", $variant?->name) }}" placeholder="Size / Color"><input name="variant_values[]" value="{{ old("variant_values.{$index}", $variant?->value) }}" placeholder="M / Red"><input type="number" min="0" name="variant_stocks[]" value="{{ old("variant_stocks.{$index}", $variant?->stock ?? 0) }}">@endfor</div></div>
            <label class="check-row"><input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}> Featured product</label>
            <label class="check-row"><input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}> New arrival</label>


        {{-- ============================= --}}
        {{-- FORM BUTTONS --}}
        {{-- ============================= --}}

        <div class="form-actions">

            <a
                href="{{ route('product.index') }}"
                class="btn btn-light"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                ✏️ Update Product
            </button>

        </div>

    </form>

</div>


{{-- ============================= --}}
{{-- IMAGE PREVIEW JAVASCRIPT --}}
{{-- ============================= --}}

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const imageInput =
            document.getElementById('image');

        const previewContainer =
            document.getElementById('imagePreviewContainer');

        const preview =
            document.getElementById('imagePreview');


        if (imageInput) {

            imageInput.addEventListener('change', function (event) {

                const file =
                    event.target.files[0];


                if (!file) {

                    previewContainer.style.display = 'none';

                    preview.src = '';

                    return;

                }


                if (!file.type.startsWith('image/')) {

                    previewContainer.style.display = 'none';

                    preview.src = '';

                    return;

                }


                const reader =
                    new FileReader();


                reader.onload = function (e) {

                    preview.src = e.target.result;

                    previewContainer.style.display = 'block';

                };


                reader.readAsDataURL(file);

            });

        }

    });

</script>


{{-- ============================= --}}
{{-- PAGE CSS --}}
{{-- ============================= --}}

<style>

    .product-form {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    }


    .form-group {
        margin-bottom: 22px;
    }


    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #111827;
    }


    .required {
        color: #dc2626;
    }


    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 13px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
    }


    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }


    .form-group textarea {
        resize: vertical;
        line-height: 1.6;
    }


    .help-text {
        display: block;
        margin-top: 7px;
        color: #6b7280;
        font-size: 13px;
    }


    .error-text {
        display: block;
        margin-top: 6px;
        color: #dc2626;
        font-size: 13px;
        font-weight: 600;
    }


    .current-image-box {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
    }


    .current-product-image {
        width: 180px;
        height: 180px;
        object-fit: contain;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
    }


    .current-image-name {
        color: #6b7280;
        font-size: 12px;
        word-break: break-all;
        max-width: 180px;
        text-align: center;
    }


    .no-current-image {
        padding: 20px;
        background: #f3f4f6;
        border-radius: 8px;
        color: #6b7280;
    }


    .image-preview-container {
        margin-top: 15px;
        padding: 15px;
        border: 1px dashed #9ca3af;
        border-radius: 10px;
        background: #f9fafb;
    }


    .image-preview-container p {
        margin: 0 0 10px;
        font-weight: 700;
        color: #374151;
    }


    .image-preview {
        width: 200px;
        height: 200px;
        object-fit: contain;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }


    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }


    .btn {
        display: inline-block;
        border: none;
        padding: 11px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        cursor: pointer;
        font-size: 14px;
    }


    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }


    .btn-light {
        background: #f3f4f6;
        color: #374151;
    }


    .btn-primary:hover,
    .btn-light:hover {
        opacity: 0.9;
    }


    .alert {
        max-width: 800px;
        margin: 0 auto 20px;
        padding: 15px 18px;
        border-radius: 8px;
    }


    .alert.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }


    @media (max-width: 600px) {

        .product-form {
            padding: 20px;
        }


        .form-actions {
            flex-direction: column;
        }


        .form-actions .btn {
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }


        .current-product-image,
        .image-preview {
            width: 150px;
            height: 150px;
        }

    }

</style>

@endsection