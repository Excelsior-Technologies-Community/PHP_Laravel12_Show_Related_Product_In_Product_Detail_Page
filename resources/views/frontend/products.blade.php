@extends('layout.app')

@section('content')

<h2>Our Products</h2>

{{-- Frontend product grid --}}
<div class="frontend-grid">

@foreach($products as $product)
    <div class="product-card modern">

        {{-- Product image --}}
        <div class="image-wrap">
            @if($product->image)
                <img src="{{ asset('products/'.$product->image) }}">
            @else
                <div class="no-image">No Image</div>
            @endif
        </div>

        {{-- Product info --}}
        <div class="card-body">
            <h4 class="product-title">{{ $product->name }}</h4>

            <p class="category">{{ $product->category->name }}</p>

            <p class="details">
                {{ \Illuminate\Support\Str::limit($product->details, 70) }}
            </p>

            {{-- Price and detail button --}}
            <div class="card-footer">
                <span class="price">
                    ₹{{ number_format($product->price,2) }}
                </span>

                <a href="{{ route('frontend.product.detail', $product->id) }}">
                    <button class="btn btn-primary btn-sm">View Details</button>
                </a>
            </div>
        </div>

    </div>
@endforeach

</div>

@endsection
