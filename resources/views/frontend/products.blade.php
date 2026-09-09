@extends('layout.app')

@section('content')

<h2>Our Products</h2>


{{-- ========================================================= --}}
{{-- PRODUCT SEARCH / FILTER / SORT --}}
{{-- ========================================================= --}}

<form
    method="GET"
    action="{{ route('frontend.products') }}"
    class="frontend-product-filter"
>

    {{-- Search --}}
    <div class="filter-group">

        <label>
            Search
        </label>

        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Search products..."
        >

    </div>


    {{-- Category --}}
    <div class="filter-group">

        <label>
            Category
        </label>

        <select name="category_id">

            <option value="">
                All Categories
            </option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>

    </div>


    {{-- Price --}}
    <div class="filter-group">

        <label>
            Price
        </label>

        <select name="price_range">

            <option value="">
                All Prices
            </option>

            <option
                value="under_500"
                {{ $priceRange === 'under_500' ? 'selected' : '' }}
            >
                Under ₹500
            </option>

            <option
                value="500_1000"
                {{ $priceRange === '500_1000' ? 'selected' : '' }}
            >
                ₹500 - ₹1,000
            </option>

            <option
                value="1000_2000"
                {{ $priceRange === '1000_2000' ? 'selected' : '' }}
            >
                ₹1,000 - ₹2,000
            </option>

            <option
                value="above_2000"
                {{ $priceRange === 'above_2000' ? 'selected' : '' }}
            >
                Above ₹2,000
            </option>

        </select>

    </div>


    {{-- Sort --}}
    <div class="filter-group">

        <label>
            Sort By
        </label>

        <select name="sort">

            <option
                value="latest"
                {{ $sort === 'latest' ? 'selected' : '' }}
            >
                Latest
            </option>

            <option
                value="price_low"
                {{ $sort === 'price_low' ? 'selected' : '' }}
            >
                Price: Low to High
            </option>

            <option
                value="price_high"
                {{ $sort === 'price_high' ? 'selected' : '' }}
            >
                Price: High to Low
            </option>

            <option
                value="name_asc"
                {{ $sort === 'name_asc' ? 'selected' : '' }}
            >
                Name: A to Z
            </option>

            <option
                value="name_desc"
                {{ $sort === 'name_desc' ? 'selected' : '' }}
            >
                Name: Z to A
            </option>

        </select>

    </div>


    {{-- Buttons --}}
    <div class="filter-buttons">

        <button
            type="submit"
            class="btn btn-primary"
        >
            Apply
        </button>

        <a
            href="{{ route('frontend.products') }}"
            class="btn btn-secondary"
        >
            Reset
        </a>

    </div>

</form>


{{-- ========================================================= --}}
{{-- RESULT COUNT --}}
{{-- ========================================================= --}}

<div class="product-result-info">

    Showing
    <strong>{{ $products->count() }}</strong>
    product(s)

</div>


{{-- ========================================================= --}}
{{-- FRONTEND PRODUCT GRID --}}
{{-- ========================================================= --}}

@if($products->count())

    <div class="frontend-grid">

        @foreach($products as $product)

            <div class="product-card modern">

                {{-- Product Image --}}
                <div class="image-wrap">

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
                <div class="card-body">

                    <h4 class="product-title">
                        {{ $product->name }}
                    </h4>


                    <p class="category">

                        {{ $product->category?->name ?? 'No Category' }}

                    </p>


                    <p class="details">

                        {{ \Illuminate\Support\Str::limit(
                            $product->details,
                            70
                        ) }}

                    </p>


                    {{-- Price + Details --}}
                    <div class="card-footer">

                        <span class="price">

                            ₹{{ number_format(
                                $product->price,
                                2
                            ) }}

                        </span>


                        <a
                            href="{{ route(
                                'frontend.product.detail',
                                $product->id
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

    <div class="product-result-info">

        No products found matching your filters.

    </div>

@endif

@endsection