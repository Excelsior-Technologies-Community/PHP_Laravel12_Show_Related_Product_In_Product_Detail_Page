@extends('layout.app')

@section('content')

<div class="card-wrapper">

    {{-- ============================= --}}
    {{-- PAGE HEADER --}}
    {{-- ============================= --}}

    <div class="page-header">

        <h2>Products</h2>

        <div class="header-actions">

            <a href="{{ route('product.create') }}">
                <button class="btn btn-primary" type="button">
                    + Add Product
                </button>
            </a>

            <a href="{{ route('product.trash') }}">
                <button class="btn btn-danger" type="button">
                    🗑 Trash
                </button>
            </a>

            <a href="{{ route('product.exportCsv', request()->query()) }}">
                <button class="btn btn-secondary" type="button">
                    📥 Export CSV
                </button>
            </a>

        </div>

    </div>


    {{-- ============================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ============================= --}}

    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ============================= --}}
    {{-- ERROR MESSAGE --}}
    {{-- ============================= --}}

    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    {{-- ============================= --}}
    {{-- FILTER --}}
    {{-- ============================= --}}

    <form
        method="GET"
        action="{{ route('product.index') }}"
        class="admin-filter"
    >

        {{-- Search --}}
        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Search product..."
        >


        {{-- Category --}}
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


        {{-- Price Range --}}
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


        {{-- Status --}}
        <select name="status">

            <option value="">
                All Status
            </option>

            <option
                value="active"
                {{ $status === 'active' ? 'selected' : '' }}
            >
                Active
            </option>

            <option
                value="inactive"
                {{ $status === 'inactive' ? 'selected' : '' }}
            >
                Inactive
            </option>

        </select>


        {{-- Sorting --}}
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


        {{-- Apply --}}
        <button
            type="submit"
            class="btn btn-primary"
        >
            Apply
        </button>


        {{-- Reset --}}
        <a
            href="{{ route('product.index') }}"
            class="btn btn-light"
        >
            Reset
        </a>

    </form>


    {{-- ============================= --}}
    {{-- BULK DELETE --}}
    {{-- ============================= --}}

    <form
        method="POST"
        action="{{ route('product.bulkDelete') }}"
        id="bulkDeleteForm"
    >

        @csrf

        <div class="bulk-bar">

            <button
                type="submit"
                class="btn btn-danger"
                onclick="return confirm('Move selected products to trash?')"
            >
                🗑 Delete Selected
            </button>

        </div>


        {{-- ============================= --}}
        {{-- PRODUCT TABLE --}}
        {{-- ============================= --}}

        <table class="table clean-table">

            <thead>

                <tr>

                    <th>
                        <input
                            type="checkbox"
                            id="selectAll"
                        >
                    </th>

                    <th>#</th>

                    <th>Name</th>

                    <th>Details</th>

                    <th>Category</th>

                    <th>Price</th>

                    <th>Status</th>

                    <th>Image</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                @forelse($products as $product)

                    <tr>

                        {{-- Checkbox --}}
                        <td>

                            <input
                                type="checkbox"
                                name="product_ids[]"
                                value="{{ $product->id }}"
                                class="product-checkbox"
                            >

                        </td>


                        {{-- Serial Number --}}
                        <td>

                            {{ $products->firstItem() + $loop->index }}

                        </td>


                        {{-- Name --}}
                        <td>

                            <strong>
                                {{ $product->name }}
                            </strong>

                        </td>


                        {{-- Details --}}
                        <td>

                            {{ Str::limit($product->details, 50) }}

                        </td>


                        {{-- Category --}}
                        <td>

                            {{ $product->category?->name ?? 'No Category' }}

                        </td>


                        {{-- Price --}}
                        <td>

                            ₹{{ number_format($product->price, 2) }}

                        </td>


                        {{-- Status --}}
                        <td>

                            @if($product->status === 'active')

                                <span class="status active">
                                    Active
                                </span>

                            @else

                                <span class="status inactive">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        {{-- Image --}}
                        <td>

                            @if($product->image)

                                <img
                                    src="{{ asset('products/'.$product->image) }}"
                                    class="table-img"
                                    alt="{{ $product->name }}"
                                >

                            @else

                                —

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td>

                            <a
                                href="{{ route('product.edit', $product->id) }}"
                            >
                                <button
                                    type="button"
                                    class="btn btn-light"
                                >
                                    Edit
                                </button>
                            </a>


                            <a
                                href="{{ route('product.delete', $product->id) }}"
                                onclick="return confirm('Move this product to trash?')"
                            >
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                >
                                    Delete
                                </button>
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="empty-text"
                        >
                            No products found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </form>


    {{-- ============================= --}}
    {{-- NUMERIC ONLY PAGINATION --}}
    {{-- ============================= --}}

    @if($products->hasPages())

        <div class="number-pagination">

            {{-- Page Numbers Only --}}
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)

                @if($page == $products->currentPage())

                    <span class="page-number active">
                        {{ $page }}
                    </span>

                @else

                    <a
                        href="{{ $url }}"
                        class="page-number"
                    >
                        {{ $page }}
                    </a>

                @endif

            @endforeach

        </div>

    @endif

</div>


{{-- ============================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================= --}}

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const selectAll = document.getElementById('selectAll');

        const checkboxes = document.querySelectorAll(
            '.product-checkbox'
        );


        if (selectAll) {

            selectAll.addEventListener('change', function () {

                checkboxes.forEach(function (checkbox) {

                    checkbox.checked = selectAll.checked;

                });

            });

        }


        checkboxes.forEach(function (checkbox) {

            checkbox.addEventListener('change', function () {

                const allChecked =
                    document.querySelectorAll(
                        '.product-checkbox:checked'
                    ).length === checkboxes.length;

                if (selectAll) {
                    selectAll.checked = allChecked;
                }

            });

        });

    });

</script>


{{-- ============================= --}}
{{-- PAGINATION CSS --}}
{{-- ============================= --}}

<style>

    .number-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 25px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }


    .page-number {
        display: inline-flex;
        justify-content: center;
        align-items: center;

        min-width: 40px;
        height: 40px;

        padding: 0 12px;

        border: 1px solid #d1d5db;
        border-radius: 8px;

        background: #ffffff;
        color: #374151;

        text-decoration: none;

        font-size: 14px;
        font-weight: 600;

        transition: all 0.2s ease;
    }


    .page-number:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }


    .page-number.active {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        cursor: default;
    }


    @media (max-width: 600px) {

        .number-pagination {
            gap: 5px;
        }

        .page-number {
            min-width: 35px;
            height: 35px;
            padding: 0 9px;
            font-size: 13px;
        }

    }

</style>

@endsection