@extends('layout.app')

@section('content')

<div class="container">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- ============================= --}}
    {{-- PRODUCT DETAIL --}}
    {{-- ============================= --}}

    <div class="product-detail-card">

        <div class="product-detail-image">

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


        <div class="product-detail-content">

            {{-- Category --}}
            @if($product->category)
                <span class="product-category">
                    {{ $product->category->name }}
                </span>
            @endif


            {{-- Product Name --}}
            <h1>
                {{ $product->name }}
            </h1>


            {{-- Price --}}
            <div class="product-detail-price">
                ₹{{ number_format($product->price, 2) }}
            </div>


            {{-- Status --}}
            @if($product->status ?? 'active' === 'active')
                <span class="status-badge active">
                    Active
                </span>
            @endif


            {{-- Details --}}
            <div class="product-description">

                <h3>Product Details</h3>

                @if($product->details)
                    <p>
                        {{ $product->details }}
                    </p>
                @else
                    <p>
                        No product details available.
                    </p>
                @endif

            </div>


            {{-- ============================= --}}
            {{-- ACTION BUTTONS --}}
            {{-- ============================= --}}

            <div class="product-detail-actions">

                {{-- Back --}}
                <a
                    href="{{ url('/') }}"
                    class="btn btn-secondary"
                >
                    ← Back to Products
                </a>


                {{-- Wishlist --}}
                @php
                    $wishlist = session('wishlist', []);
                    $isWishlisted = in_array($product->id, $wishlist);
                @endphp


                @if($isWishlisted)

                    {{-- Remove from Wishlist --}}
                    <form
                        action="{{ route('wishlist.remove', $product->id) }}"
                        method="POST"
                        style="display:inline;"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn wishlist-btn wishlist-active"
                        >
                            ❤️ Remove from Wishlist
                        </button>

                    </form>

                @else

                    {{-- Add to Wishlist --}}
                    <form
                        action="{{ route('wishlist.add', $product->id) }}"
                        method="POST"
                        style="display:inline;"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn wishlist-btn"
                        >
                            🤍 Add to Wishlist
                        </button>

                    </form>

                @endif

            </div>

        </div>

    </div>



    {{-- ============================= --}}
    {{-- RELATED PRODUCTS --}}
    {{-- ============================= --}}

    @if(isset($relatedProducts) && $relatedProducts->count())

        <div class="section-heading">

            <h2>
                Related Products
            </h2>

            <p>
                Products from the same category
            </p>

        </div>


        <div class="frontend-grid">

            @foreach($relatedProducts as $relatedProduct)

                <div class="product-card modern">

                    {{-- Image --}}
                    <div class="product-card-image">

                        @if($relatedProduct->image)

                            <img
                                src="{{ asset('products/' . $relatedProduct->image) }}"
                                alt="{{ $relatedProduct->name }}"
                            >

                        @else

                            <div class="no-image">
                                No Image
                            </div>

                        @endif

                    </div>


                    {{-- Content --}}
                    <div class="product-card-content">

                        @if($relatedProduct->category)

                            <span class="product-category">
                                {{ $relatedProduct->category->name }}
                            </span>

                        @endif


                        <h3>
                            {{ $relatedProduct->name }}
                        </h3>


                        <p class="product-card-details">

                            {{ \Illuminate\Support\Str::limit(
                                $relatedProduct->details ?? 'No details available',
                                80
                            ) }}

                        </p>


                        <div class="product-card-bottom">

                            <strong>
                                ₹{{ number_format($relatedProduct->price, 2) }}
                            </strong>


                            <a
                                href="{{ route('product.detail', $relatedProduct->id) }}"
                                class="btn btn-primary"
                            >
                                View Details
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <p>
                No related products found.
            </p>

        </div>

    @endif



    {{-- ============================= --}}
    {{-- RECENTLY VIEWED PRODUCTS --}}
    {{-- ============================= --}}

    @if(isset($recentlyViewed) && $recentlyViewed->count())

        <div class="section-heading recently-heading">

            <h2>
                Recently Viewed
            </h2>

            <p>
                Products you viewed recently
            </p>

        </div>


        <div class="frontend-grid">

            @foreach($recentlyViewed as $recentProduct)

                {{-- Don't show current product --}}
                @if($recentProduct->id != $product->id)

                    <div class="product-card modern">

                        {{-- Image --}}
                        <div class="product-card-image">

                            @if($recentProduct->image)

                                <img
                                    src="{{ asset('products/' . $recentProduct->image) }}"
                                    alt="{{ $recentProduct->name }}"
                                >

                            @else

                                <div class="no-image">
                                    No Image
                                </div>

                            @endif

                        </div>


                        {{-- Content --}}
                        <div class="product-card-content">

                            @if($recentProduct->category)

                                <span class="product-category">
                                    {{ $recentProduct->category->name }}
                                </span>

                            @endif


                            <h3>
                                {{ $recentProduct->name }}
                            </h3>


                            <p class="product-card-details">

                                {{ \Illuminate\Support\Str::limit(
                                    $recentProduct->details ?? 'No details available',
                                    80
                                ) }}

                            </p>


                            <div class="product-card-bottom">

                                <strong>
                                    ₹{{ number_format($recentProduct->price, 2) }}
                                </strong>


                                <a
                                    href="{{ route('product.detail', $recentProduct->id) }}"
                                    class="btn btn-primary"
                                >
                                    View Details
                                </a>

                            </div>

                        </div>

                    </div>

                @endif

            @endforeach

        </div>

    @endif

</div>



{{-- ============================= --}}
{{-- PAGE CSS --}}
{{-- ============================= --}}

<style>

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }


    /* Alert */

    .alert {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }


    /* Product Detail */

    .product-detail-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        background: #ffffff;
        border-radius: 18px;
        padding: 30px;
        margin-bottom: 50px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }


    .product-detail-image {
        width: 100%;
        min-height: 420px;
        border-radius: 15px;
        overflow: hidden;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }


    .product-detail-image img {
        width: 100%;
        height: 420px;
        object-fit: contain;
        display: block;
    }


    .product-detail-content {
        padding: 15px 5px;
    }


    .product-detail-content h1 {
        font-size: 36px;
        margin: 15px 0;
        color: #111827;
    }


    .product-category {
        display: inline-block;
        background: #eef2ff;
        color: #4338ca;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 10px;
    }


    .product-detail-price {
        font-size: 30px;
        font-weight: 800;
        margin: 20px 0;
        color: #059669;
    }


    .product-description {
        margin-top: 30px;
    }


    .product-description h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #111827;
    }


    .product-description p {
        line-height: 1.8;
        color: #4b5563;
        font-size: 16px;
    }


    /* Status */

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }


    .status-badge.active {
        background: #dcfce7;
        color: #166534;
    }


    /* Buttons */

    .product-detail-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 30px;
    }


    .btn {
        display: inline-block;
        border: none;
        padding: 11px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s ease;
        font-size: 14px;
    }


    .btn:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }


    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }


    .btn-secondary {
        background: #6b7280;
        color: #ffffff;
    }


    /* Wishlist */

    .wishlist-btn {
        background: #f3f4f6;
        color: #111827;
    }


    .wishlist-btn:hover {
        background: #e5e7eb;
    }


    .wishlist-active {
        background: #fee2e2;
        color: #b91c1c;
    }


    .wishlist-active:hover {
        background: #fecaca;
    }


    /* Section Heading */

    .section-heading {
        margin: 40px 0 20px;
    }


    .section-heading h2 {
        margin: 0;
        font-size: 28px;
        color: #111827;
    }


    .section-heading p {
        margin-top: 6px;
        color: #6b7280;
    }


    .recently-heading {
        margin-top: 60px;
    }


    /* Product Grid */

    .frontend-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }


    .product-card {
        background: #ffffff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        transition: 0.25s ease;
    }


    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }


    .product-card-image {
        height: 230px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }


    .product-card-image img {
        width: 100%;
        height: 230px;
        object-fit: contain;
    }


    .product-card-content {
        padding: 20px;
    }


    .product-card-content h3 {
        margin: 8px 0;
        font-size: 20px;
        color: #111827;
    }


    .product-card-details {
        min-height: 45px;
        color: #6b7280;
        line-height: 1.5;
        font-size: 14px;
    }


    .product-card-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 18px;
    }


    .product-card-bottom strong {
        font-size: 18px;
        color: #059669;
        white-space: nowrap;
    }


    /* No Image */

    .no-image {
        color: #9ca3af;
        font-weight: 600;
        text-align: center;
        padding: 20px;
    }


    /* Empty */

    .empty-state {
        padding: 30px;
        background: #f9fafb;
        border-radius: 12px;
        text-align: center;
        color: #6b7280;
        margin-bottom: 30px;
    }


    /* Responsive */

    @media (max-width: 900px) {

        .product-detail-card {
            grid-template-columns: 1fr;
        }

        .frontend-grid {
            grid-template-columns: repeat(2, 1fr);
        }

    }


    @media (max-width: 600px) {

        .container {
            padding: 20px 12px;
        }

        .product-detail-card {
            padding: 18px;
            gap: 20px;
        }

        .product-detail-image {
            min-height: 300px;
        }

        .product-detail-image img {
            height: 300px;
        }

        .product-detail-content h1 {
            font-size: 28px;
        }

        .product-detail-price {
            font-size: 25px;
        }

        .frontend-grid {
            grid-template-columns: 1fr;
        }

        .product-card-bottom {
            flex-direction: column;
            align-items: flex-start;
        }

        .product-detail-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .product-detail-actions .btn,
        .product-detail-actions form,
        .product-detail-actions form .btn {
            width: 100%;
            text-align: center;
        }

    }

</style>

@endsection