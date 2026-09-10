@extends('layout.app')

@section('content')

<div class="frontend-products-header">

    <div>

        <h2>
            ❤️ My Wishlist
        </h2>

        <p class="frontend-subtitle">
            Products you saved for later.
        </p>

    </div>

</div>


@if($products->count())

    <div class="frontend-grid">

        @foreach($products as $product)

            <div class="product-card modern">

                <div class="image-wrap">

                    @if($product->image)

                        <img
                            src="{{ asset(
                                'products/' . $product->image
                            ) }}"
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

                        {{ $product->category?->name
                            ?? 'No Category' }}

                    </p>


                    <p class="price">

                        ₹{{ number_format(
                            $product->price,
                            2
                        ) }}

                    </p>


                    <div class="card-footer">

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
                                View
                            </button>

                        </a>


                        <a
                            href="{{ route(
                                'wishlist.remove',
                                $product->id
                            ) }}"
                        >

                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                            >
                                Remove
                            </button>

                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@else

    <div class="product-result-info">

        ❤️ Your wishlist is empty.

    </div>

@endif

@endsection