@extends('layout.app')

@section('content')

<div class="card-wrapper">

    <div class="page-header">

        <h2>🗑 Product Trash</h2>

        <a href="{{ route('product.index') }}">
            <button class="btn btn-secondary">
                ← Back to Products
            </button>
        </a>

    </div>


    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    <table class="table clean-table">

        <thead>

            <tr>

                <th>#</th>

                <th>Name</th>

                <th>Category</th>

                <th>Price</th>

                <th>Deleted At</th>

                <th>Action</th>

            </tr>

        </thead>


        <tbody>

            @forelse($products as $product)

                <tr>

                    <td>
                        {{ $products->firstItem() + $loop->index }}
                    </td>

                    <td>
                        {{ $product->name }}
                    </td>

                    <td>
                        {{ $product->category?->name ?? 'No Category' }}
                    </td>

                    <td>
                        ₹{{ number_format($product->price, 2) }}
                    </td>

                    <td>
                        {{ $product->deleted_at }}
                    </td>

                    <td>

                        <a
                            href="{{ route('product.restore', $product->id) }}"
                            onclick="return confirm('Restore this product?')"
                        >

                            <button
                                type="button"
                                class="btn btn-primary"
                            >
                                ♻ Restore
                            </button>

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="empty-text"
                    >
                        Trash is empty.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="pagination-wrapper">

        {{ $products->links() }}

    </div>

</div>

@endsection