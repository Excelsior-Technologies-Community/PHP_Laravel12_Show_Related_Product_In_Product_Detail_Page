@extends('layout.app')

@section('content')
<div class="card-wrapper">
    <div class="page-header"><h2>Review & Question Approval</h2><a class="btn btn-light" href="{{ route('product.index') }}">Products</a></div>
    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
    <h3>Reviews</h3>
    <table><thead><tr><th>Product</th><th>Customer</th><th>Rating</th><th>Review</th><th>Status</th><th>Action</th></tr></thead><tbody>
    @forelse($reviews as $review)<tr><td>{{ $review->product?->name }}</td><td>{{ $review->customer_name }}</td><td>{{ $review->rating }}/5</td><td>{{ $review->review }}</td><td>{{ $review->is_approved ? 'Approved' : 'Pending' }}</td><td>@if(!$review->is_approved)<form method="POST" action="{{ route('product.reviews.approve', $review->id) }}">@csrf<button class="btn btn-primary btn-sm">Approve</button></form>@endif</td></tr>@empty<tr><td colspan="6">No reviews found.</td></tr>@endforelse
    </tbody></table>
    <h3>Questions</h3>
    <table><thead><tr><th>Product</th><th>Customer</th><th>Question</th><th>Status</th><th>Action</th></tr></thead><tbody>
    @forelse($questions as $question)<tr><td>{{ $question->product?->name }}</td><td>{{ $question->customer_name }}</td><td>{{ $question->question }}</td><td>{{ $question->is_approved ? 'Approved' : 'Pending' }}</td><td><form method="POST" action="{{ route('product.questions.answer', $question->id) }}">@csrf<textarea name="answer" placeholder="Answer">{{ $question->answer }}</textarea><button class="btn btn-primary btn-sm">Save answer</button></form></td></tr>@empty<tr><td colspan="5">No questions found.</td></tr>@endforelse
    </tbody></table>
</div>
@endsection