@extends('layout.app')

@section('content')
<div class="frontend-products-header"><div><h2>Compare Products</h2><p class="frontend-subtitle">Compare price, brand, stock and category.</p></div></div>
@if($products->count())
<div style="overflow-x:auto"><table><thead><tr><th>Product</th>@foreach($products as $product)<th>{{ $product->name }}</th>@endforeach</tr></thead><tbody>
<tr><th>Category</th>@foreach($products as $product)<td>{{ $product->category?->name }}</td>@endforeach</tr>
<tr><th>Brand</th>@foreach($products as $product)<td>{{ $product->brand ?: '-' }}</td>@endforeach</tr>
<tr><th>Price</th>@foreach($products as $product)<td>₹{{ number_format($product->selling_price, 2) }}</td>@endforeach</tr>
<tr><th>Stock</th>@foreach($products as $product)<td>{{ $product->stock > 0 ? 'Available' : 'Out of stock' }}</td>@endforeach</tr>
<tr><th>Action</th>@foreach($products as $product)<td><form method="POST" action="{{ route('compare.remove', $product->id) }}">@csrf<button class="btn btn-danger btn-sm">Remove</button></form></td>@endforeach</tr>
</tbody></table></div>
@else<div class="product-result-info">No products selected for comparison.</div>@endif
@endsection