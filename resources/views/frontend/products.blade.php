@extends('layout.app')

@section('content')

<h2>Our Products</h2>


{{-- FILTER --}}

<form
    method="GET"
    action="{{ route('frontend.products') }}"
    class="frontend-product-filter"
>

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
                    {{ (string)$categoryId === (string)$category->id ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="filter-group">

        <label>
            Price Range
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


    {{-- NEW MIN PRICE --}}

    <div class="filter-group">

        <label>
            Min Price
        </label>

        <input
            type="number"
            name="min_price"
            value="{{ $minPrice }}"
            min="0"
            placeholder="₹ Minimum"
        >

    </div>


    {{-- NEW MAX PRICE --}}

    <div class="filter-group">

        <label>
            Max Price
        </label>

        <input
            type="number"
            name="max_price"
            value="{{ $maxPrice }}"
            min="0"
            placeholder="₹ Maximum"
        >

    </div>


    <div class="filter-group">

        <label>
            Sort
        </label>

        <select name="sort">

            <option
                value="latest"
                {{ $sort === 'latest' ? 'selected' : '' }}
            >
                Latest
            </option>

            <option
                value="oldest"
                {{ $sort === 'oldest' ? 'selected' : '' }}
            >
                Oldest
            </option>

            <option
                value="price_low"
                {{ $sort === 'price_low' ? 'selected' : '' }}
            >
                Price Low to High
            </option>

            <option
                value="price_high"
                {{ $sort === 'price_high' ? 'selected' : '' }}
            >
                Price High to Low
            </option>

            <option
                value="name_asc"
                {{ $sort === 'name_asc' ? 'selected' : '' }}
            >
                Name A-Z
            </option>

            <option
                value="name_desc"
                {{ $sort === 'name_desc' ? 'selected' : '' }}
            >
                Name Z-A
            </option>

        </select>

    </div>


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


<div class="product-result-info">

    Showing

    <strong>
        {{ $products->total() }}
    </strong>

    product(s)

</div>


@if($products->count())

    <div class="frontend-grid">

        @foreach($products as $product)

            <div class="product-card modern">

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


                <div class="card-body">

                    <h4 class="product-title">
                        {{ $product->name }}
                    </h4>


                    <p class="category">
                        {{ $product->category?->name ?? 'No Category' }}
                    </p>


                    <p class="details">

                        {{ Str::limit(
                            $product->details,
                            70
                        ) }}

                    </p>


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


    {{-- PAGINATION --}}

    <div class="pagination-wrapper">

        {{ $products->links() }}

    </div>

@else

    <div class="product-result-info">
        No products found matching your filters.
    </div>

@endif

@endsection