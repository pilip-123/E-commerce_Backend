@extends('layouts.admin')

@section('title', __('Stock Count'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Physical Stock Count') }}</h5>
                <small class="text-muted">{{ __('Record actual quantities for inventory verification') }}</small>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.inventory.stock-count.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('Notes (optional)') }}</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('System Stock') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actual Quantity') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Difference') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                     class="rounded-3 border flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover;">
                                            @else
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light border text-muted flex-shrink-0"
                                                       style="width: 36px; height: 36px; font-size: 9px;">{{ __('N/A') }}</span>
                                             @endif
                                             <span class="fw-semibold">{{ $product->name }}</span>
                                             <span class="text-muted small">({{ $product->category->name ?? __('N/A') }})</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end fw-bold system-stock">{{ $product->stock }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <input type="hidden" name="counts[{{ $loop->index }}][product_id]" value="{{ $product->id }}">
                                        <input type="number" name="counts[{{ $loop->index }}][actual_quantity]"
                                               class="form-control form-control-sm text-end actual-qty"
                                               style="width: 120px; display: inline-block;"
                                               value="{{ old("counts.{$loop->index}.actual_quantity", $product->stock) }}" min="0">
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <span class="diff-badge fw-bold badge bg-secondary-subtle text-secondary-emphasis px-3 py-2">0</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-clipboard-check me-1"></i>{{ __('Complete Stock Count') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('.actual-qty').forEach(function(input) {
    input.addEventListener('input', function() {
        let row = this.closest('tr');
        let systemStock = parseInt(row.querySelector('.system-stock').textContent) || 0;
        let actual = parseInt(this.value) || 0;
        let diff = actual - systemStock;
        let badge = row.querySelector('.diff-badge');
        badge.textContent = diff > 0 ? '+' + diff : diff;
        badge.className = 'diff-badge fw-bold badge px-3 py-2';
        if (diff < 0) {
            badge.classList.add('bg-danger-subtle', 'text-danger-emphasis');
        } else if (diff > 0) {
            badge.classList.add('bg-success-subtle', 'text-success-emphasis');
        } else {
            badge.classList.add('bg-secondary-subtle', 'text-secondary-emphasis');
        }
    });
});
</script>
@endsection
