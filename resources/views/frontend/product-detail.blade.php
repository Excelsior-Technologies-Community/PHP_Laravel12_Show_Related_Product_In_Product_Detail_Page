@extends('layout.app')

@section('content')

<div class="product-detail-wrapper">


    {{-- ========================================================= --}}
    {{-- PRODUCT DETAIL --}}
    {{-- ========================================================= --}}

    <div class="product-detail-grid">


        {{-- Product Image --}}
        <div class="detail-image-box">

            @if($product->image)

                <img
                    src="{{ asset('products/' . $product->image) }}"
                    alt="{{ $product->name }}"
                >

            @else

                <div class="no-image">
                    No Image
                </div>

            @endif

        </div>


        {{-- Product Information --}}
        <div class="detail-content-box">

            <h2 class="detail-title">

                {{ $product->name }}

            </h2>


            <p class="detail-category">

                Category:

                <span>

                    {{ $product->category?->name ?? 'No Category' }}

                </span>

            </p>


            <p class="price-label">
                Price
            </p>


            <p class="detail-price">

                ₹{{ number_format(
                    $product->price,
                    2
                ) }}

            </p>


            <div class="detail-description">

                <h4>
                    Product Details
                </h4>

                <p>

                    {{ $product->details }}

                </p>

            </div>


            <div class="detail-actions">

                <a
                    href="{{ route('frontend.products') }}"
                >

                    <button
                        type="button"
                        class="btn btn-secondary"
                    >
                        ← Back to Products
                    </button>

                </a>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- FEATURE 1: RELATED PRODUCTS --}}
    {{-- ========================================================= --}}

    <div class="related-products-section">


        <div class="related-header">

            <div>

                <h3>
                    🔗 Related Products
                </h3>

                <p class="related-subtitle">

                    Explore more products from the same category

                </p>

            </div>

        </div>


        @if($relatedProducts->count())

            <div class="frontend-grid">

                @foreach($relatedProducts as $item)

                    <div class="product-card modern">


                        {{-- Image --}}
                        <div class="image-wrap">

                            @if($item->image)

                                <img
                                    src="{{ asset(
                                        'products/' .
                                        $item->image
                                    ) }}"
                                    alt="{{ $item->name }}"
                                >

                            @else

                                <div class="no-image">
                                    No Image
                                </div>

                            @endif

                        </div>


                        {{-- Information --}}
                        <div class="card-body">

                            <h4 class="product-title">

                                {{ $item->name }}

                            </h4>


                            <p class="category">

                                {{ $item->category?->name ?? 'No Category' }}

                            </p>


                            <p class="details">

                                {{ \Illuminate\Support\Str::limit(
                                    $item->details,
                                    70
                                ) }}

                            </p>


                            <div class="card-footer">

                                <span class="price">

                                    ₹{{ number_format(
                                        $item->price,
                                        2
                                    ) }}

                                </span>


                                <a
                                    href="{{ route(
                                        'frontend.product.detail',
                                        $item->id
                                    ) }}"
                                >

                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm"
                                    >
                                        View Details
                                    </button>

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="related-result-info">

                No related products found in this category.

            </div>

        @endif

    </div>



    {{-- ========================================================= --}}
    {{-- FEATURE 2: RECENTLY VIEWED PRODUCTS --}}
    {{-- ========================================================= --}}

    @if($recentlyViewedProducts->count())

        <div class="recently-viewed-section">


            <div class="related-header">

                <div>

                    <h3>
                        🕘 Recently Viewed Products
                    </h3>

                    <p class="related-subtitle">

                        Products you viewed recently

                    </p>

                </div>

            </div>


            <div class="frontend-grid">

                @foreach(
                    $recentlyViewedProducts
                    as $item
                )

                    <div class="product-card modern">


                        {{-- Image --}}
                        <div class="image-wrap">

                            @if($item->image)

                                <img
                                    src="{{ asset(
                                        'products/' .
                                        $item->image
                                    ) }}"
                                    alt="{{ $item->name }}"
                                >

                            @else

                                <div class="no-image">
                                    No Image
                                </div>

                            @endif

                        </div>


                        {{-- Information --}}
                        <div class="card-body">

                            <h4 class="product-title">

                                {{ $item->name }}

                            </h4>


                            <p class="category">

                                {{ $item->category?->name ?? 'No Category' }}

                            </p>


                            <p class="price">

                                ₹{{ number_format(
                                    $item->price,
                                    2
                                ) }}

                            </p>


                            <a
                                href="{{ route(
                                    'frontend.product.detail',
                                    $item->id
                                ) }}"
                            >

                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                >
                                    View Again
                                </button>

                            </a>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif

</div>

@endsection