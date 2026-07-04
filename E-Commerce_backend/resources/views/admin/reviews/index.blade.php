@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-star text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Reviews</p>
                        <h5 class="fw-bold mb-0">{{ $reviews->total() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-people text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Reviewers</p>
                        <h5 class="fw-bold mb-0">{{ $reviews->pluck('user_id')->unique()->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0">All Reviews</h5>
                <small class="text-muted">{{ $reviews->total() }} total</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.export.reviews') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i>Export
                </a>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#announcementModal">
                    <i class="bi bi-megaphone me-1"></i>Notify Customers
                </button>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <option value="{{ route('admin.reviews.index') }}">All Ratings</option>
                    @foreach ([5,4,3,2,1] as $r)
                        <option value="{{ route('admin.reviews.index', ['rating' => $r]) }}" {{ request('rating') == $r ? 'selected' : '' }}>
                            {{ $r }} Star{{ $r !== 1 ? 's' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">User</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Product</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Rating</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Comment</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Date</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted small">#{{ $review->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($review->user?->image_url)
                                            <img src="{{ asset('storage/' . $review->user->image_url) }}" alt="{{ $review->user->name }}"
                                                 class="rounded-circle flex-shrink-0"
                                                 style="width: 34px; height: 34px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold flex-shrink-0"
                                                 style="width: 34px; height: 34px; font-size: 12px;">
                                                {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="fw-semibold mb-0 small">{{ $review->user->name ?? 'Deleted User' }}</p>
                                            <span class="text-muted" style="font-size: 11px;">{{ $review->user->email ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.products.show', $review->product_id) }}" class="fw-semibold small text-decoration-none">{{ $review->product->name ?? 'Deleted Product' }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #f59e0b; font-size: 14px; white-space: nowrap;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)&#9733;@else&#9734;@endif
                                        @endfor
                                        <span class="text-muted ms-1" style="font-size: 11px;">{{ $review->rating }}/5</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-muted small" style="max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $review->comment ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted small">
                                    {{ $review->created_at?->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.reviews.destroy', $review->id) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">
                                    <i class="bi bi-star fs-2 d-block mb-2 text-muted"></i>
                                    No reviews found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($reviews->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
{{-- Announcement Modal --}}
<div class="modal fade" id="announcementModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form id="announcementForm" method="POST">
                @csrf
                <div class="modal-body py-4 px-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 flex-shrink-0"
                             style="width: 56px; height: 56px;">
                            <i class="bi bi-megaphone-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Notify Customers</h5>
                            <p class="text-muted small mb-0">Send an announcement to all customers.</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. New Season Sale" required maxlength="255">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Message</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Write your announcement..." required maxlength="5000"></textarea>
                    </div>
                    <div id="announcementFeedback" class="form-success d-none"></div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4" id="announcementSubmit">
                        <i class="bi bi-send me-1"></i>Send to All Customers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('announcementForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('announcementSubmit');
        const feedback = document.getElementById('announcementFeedback');
        const formData = new FormData(form);

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
        feedback.classList.add('d-none');

        try {
            const response = await fetch('{{ route('admin.notifications.announcement') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const data = await response.json();
            if (data.success) {
                feedback.className = 'alert alert-success py-2 px-3 small mb-0 mt-2';
                feedback.textContent = data.message ?? 'Announcement sent!';
                feedback.classList.remove('d-none');
                form.querySelector('input[name="title"]').value = '';
                form.querySelector('textarea[name="message"]').value = '';
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('announcementModal'))?.hide();
                    feedback.classList.add('d-none');
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to send.');
            }
        } catch (err) {
            feedback.className = 'alert alert-danger py-2 px-3 small mb-0 mt-2';
            feedback.textContent = err.message || 'Something went wrong.';
            feedback.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send me-1"></i>Send to All Customers';
        }
    });

    document.getElementById('announcementModal')?.addEventListener('hidden.bs.modal', function() {
        const feedback = document.getElementById('announcementFeedback');
        feedback.classList.add('d-none');
        feedback.className = 'form-success d-none';
    });
</script>
@endpush
@endsection
