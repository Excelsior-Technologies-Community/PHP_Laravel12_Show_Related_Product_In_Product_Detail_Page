@extends('layout.app')

@section('content')

<div class="card-wrapper">

    <div class="page-header">

        <h2>Categories</h2>

        <a href="{{ route('categories.create') }}">

            <button class="btn btn-primary">
                + Add Category
            </button>

        </a>

    </div>


    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    {{-- CATEGORY SEARCH --}}

    <form
        method="GET"
        action="{{ route('categories.index') }}"
        class="category-search"
    >

        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Search category..."
        >

        <button
            type="submit"
            class="btn btn-primary"
        >
            🔎 Search
        </button>

        <a
            href="{{ route('categories.index') }}"
            class="btn btn-light"
        >
            Reset
        </a>

    </form>


    <table class="table clean-table">

        <thead>

            <tr>

                <th>#</th>

                <th>Category Name</th>

                <th>Products</th>

                <th>Actions</th>

            </tr>

        </thead>


        <tbody>

            @forelse($categories as $category)

                <tr>

                    <td>
                        {{ $categories->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <strong>
                            {{ $category->name }}
                        </strong>
                    </td>

                    <td>
                        {{ $category->products_count }}
                    </td>

                    <td>

                        <a
                            href="{{ route('categories.edit', $category->id) }}"
                        >

                            <button
                                type="button"
                                class="btn btn-light"
                            >
                                Edit
                            </button>

                        </a>


                        <a
                            href="{{ route('categories.delete', $category->id) }}"
                            onclick="return confirm('Delete this category?')"
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
                        colspan="4"
                        class="empty-text"
                    >
                        No categories found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="pagination-wrapper">

        {{ $categories->links() }}

    </div>

</div>

@endsection