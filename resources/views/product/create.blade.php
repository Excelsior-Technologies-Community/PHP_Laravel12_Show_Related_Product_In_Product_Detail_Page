@extends('layout.app')

@section('content')

<div class="card-wrapper">
    <div class="page-header"><h2>Add Product</h2><a href="{{ route('product.index') }}" class="btn btn-light">Back</a></div>
    @if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data" class="product-form">
        @csrf
        <div class="form-grid">
            <div class="form-group"><label>Product Name *</label><input name="name" value="{{ old('name') }}" required></div>
            <div class="form-group"><label>SKU / Product Code</label><input name="sku" value="{{ old('sku') }}"></div>
            <div class="form-group"><label>Brand</label><input name="brand" value="{{ old('brand') }}"></div>
            <div class="form-group"><label>Category *</label><select name="category_id" required><option value="">Select Category</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Regular Price *</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required></div>
            <div class="form-group"><label>Sale / Discount Price</label><input type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price') }}"></div>
            <div class="form-group"><label>Stock *</label><input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required></div>
            <div class="form-group"><label>Status *</label><select name="status" required><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
        </div>
        <div class="form-group"><label>Tags <small>(comma separated)</small></label><input name="tags" value="{{ old('tags') }}" placeholder="summer, cotton, trending"></div>
        <div class="form-group"><label>Product Details</label><textarea name="details">{{ old('details') }}</textarea></div>
        <div class="form-group"><label>Main Product Image</label><input type="file" name="image" accept="image/*"></div>
        <div class="form-group"><label>Additional Product Images</label><input type="file" name="images[]" accept="image/*" multiple></div>
        <div class="form-group"><label>Variants</label><div class="variant-grid"><strong>Type</strong><strong>Value</strong><strong>Stock</strong>@for($index = 0; $index < 4; $index++)<input name="variant_names[]" placeholder="Size / Color"><input name="variant_values[]" placeholder="M / Red"><input type="number" min="0" name="variant_stocks[]" value="0">@endfor</div></div>
        <label class="check-row"><input type="checkbox" name="featured" value="1"> Featured product</label>
        <label class="check-row"><input type="checkbox" name="is_new_arrival" value="1"> New arrival</label>
        <div class="form-actions"><button class="btn btn-primary">Save Product</button><a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a></div>
    </form>
</div>

@endsection
