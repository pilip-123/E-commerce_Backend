@extends('layouts.admin')

@section('title', $product->name)

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">

        {{-- Image Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-5 px-4">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                             class="rounded-4 border border-2 border-success-subtle mb-3"
                             style="width: 100%; max-width: 240px; height: 240px; object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center rounded-4 bg-light border mb-3"
                             style="width: 100%; max-width: 240px; height: 240px;">
                            <svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 4-4 4 4 4-4 4 4"/><path d="M3 15h18"/>
                            </svg>
                        </div>
                    @endif

                    <h5 class="fw-bold mb-1 text-center">{{ $product->name }}</h5>

                    @if ($product->category)
                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 rounded-pill mt-1">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    <hr class="w-100 my-4">

                    <div class="d-flex gap-4 text-center w-100 justify-content-center flex-wrap">
                        <div>
                            @php $discountPrice = $product->getDiscountPrice(); @endphp
                            @if ($discountPrice)
                                <p class="fw-bold fs-5 mb-0">
                                    <s class="text-muted small">${{ number_format($product->price, 2) }}</s>
                                    <span class="text-danger">${{ number_format($discountPrice, 2) }}</span>
                                </p>
                            @else
                                <p class="fw-bold fs-5 mb-0">${{ number_format($product->price, 2) }}</p>
                            @endif
                            <span class="text-muted small">{{ __('Price') }}</span>
                        </div>
                        <div class="vr"></div>
                        <div>
                            @if ($product->stock > 0)
                                <p class="fw-bold fs-5 mb-0 text-success">{{ $product->stock }}</p>
                                <span class="text-muted small">{{ __('In Stock') }}</span>
                            @else
                                <p class="fw-bold fs-5 mb-0 text-danger">0</p>
                                <span class="text-muted small">{{ __('Out of Stock') }}</span>
                            @endif
                        </div>
                        <div class="vr"></div>
                        <div>
                            <p class="fw-bold fs-5 mb-0">
                                @if ($product->status)
                                    <span class="text-success">{{ __('Active') }}</span>
                                @else
                                    <span class="text-danger">{{ __('Hidden') }}</span>
                                @endif
                            </p>
                            <span class="text-muted small">{{ __('Status') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 rounded-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 fs-6">
                        <i class="bi bi-box text-success me-2"></i>{{ __('Product Information') }}
                    </h5>
                    <span class="text-muted small">{{ __('Created') }} {{ $product->created_at?->format('M d, Y') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-tag me-1"></i>{{ __('Product Name') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3">{{ $product->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-link me-1"></i>{{ __('Slug') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3">{{ $product->slug }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-grid me-1"></i>{{ __('Category') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3">{{ $product->category->name ?? __('Uncategorized') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-currency-dollar me-1"></i>{{ __('Price') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3 fw-bold">
                                @php $discountPrice = $product->getDiscountPrice(); @endphp
                                @if ($discountPrice)
                                    <s class="text-muted small">${{ number_format($product->price, 2) }}</s>
                                    <span class="text-danger ms-1">${{ number_format($discountPrice, 2) }}</span>
                                @else
                                    <span class="text-success">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-box-seam me-1"></i>{{ __('Stock') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3">{{ $product->stock }} {{ __('units') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-eye me-1"></i>{{ __('Status') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3">
                                @if ($product->status)
                                    <span class="badge bg-success-subtle text-success-emphasis">{{ __('Published') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis">{{ __('Hidden') }}</span>
                                @endif
                            </p>
                        </div>
                        @if ($product->relationLoaded('activePromotions') && $product->activePromotions->isNotEmpty())
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted mb-1">
                                    <i class="bi bi-percent me-1"></i>{{ __('Active Promotions') }}
                                </label>
                                <div class="d-flex flex-wrap gap-2 mb-0 bg-light rounded-3 p-3">
                                    @foreach ($product->activePromotions as $promo)
                                        <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">
                                            {{ $promo->name }}
                                            ({{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : '$' . number_format($promo->discount_value, 2) }} {{ __('off') }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-card-text me-1"></i>{{ __('Description') }}
                            </label>
                            <p class="mb-0 bg-light rounded-3 p-3">{{ $product->description ?? __('No description provided.') }}</p>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-success btn-sm px-4">
                            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                        </a>
                        <button type="button" class="btn btn-danger btn-sm px-4"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-url="{{ route('admin.products.destroy', $product->id) }}">
                            <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm px-4">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Reviews --}}
            @if ($product->reviews && $product->reviews->count() > 0)
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white py-3 rounded-4">
                        <h5 class="fw-bold mb-0 fs-6">
                            <i class="bi bi-star text-warning me-2"></i>{{ __('Reviews') }} ({{ $product->reviews->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            @foreach ($product->reviews as $review)
                                <div class="d-flex gap-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    @if ($review->user?->image_url)
                                        <img src="{{ asset('storage/' . $review->user->image_url) }}" alt="{{ $review->user->name }}"
                                             class="rounded-circle flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold flex-shrink-0"
                                             style="width: 36px; height: 36px; font-size: 12px;">
                                            {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="fw-semibold small">{{ $review->user->name ?? __('Anonymous') }}</span>
                                            <span style="color: #f59e0b; font-size: 13px;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->rating)
                                                        &#9733;
                                                    @else
                                                        &#9734;
                                                    @endif
                                                @endfor
                                            </span>
                                            <span class="text-muted" style="font-size: 11px;">{{ $review->created_at?->diffForHumans() }}</span>
                                        </div>
                                        @if ($review->comment)
                                            <p class="mb-0 mt-1 small text-muted">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
