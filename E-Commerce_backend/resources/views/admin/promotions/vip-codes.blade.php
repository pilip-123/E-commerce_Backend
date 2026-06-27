@extends('layouts.admin')

@section('title', 'VIP Codes')

@section('content')
<div class="container-fluid p-0">
    {{-- Generated Code Alert --}}
    @if (session('generated_code'))
        <div class="alert alert-success border-0 rounded-4 d-flex align-items-center gap-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-4"></i>
            <div>
                <strong>New VIP Code Generated:</strong>
                <span class="font-monospace fw-bold ms-2" style="font-size: 1.2rem; letter-spacing: 2px;">{{ session('generated_code') }}</span>
                <button class="btn btn-sm btn-outline-success ms-3" onclick="navigator.clipboard.writeText('{{ session('generated_code') }}').then(() => { this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 2000); })">Copy</button>
                @if (session('sent_count'))
                    <span class="ms-3 badge text-success border border-success" style="background: var(--admin-surface);">Sent to {{ session('sent_count') }} customer(s)</span>
                @endif
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VIP Codes Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">VIP Discount Codes</h5>
                <small class="text-muted">Generate special discount codes for your best customers</small>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                {{-- Form --}}
                <div class="col-lg-12">
                    <form action="{{ route('admin.promotions.vip-codes.generate') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            {{-- Left: Discount Settings --}}
                            <div class="col-lg-5">
                                <div class="card border-0 h-100 vip-card-green">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <h6 class="fw-bold mb-3 vip-text-dark-green">
                                            <i class="bi bi-lock me-2"></i>Generate New Code
                                        </h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Discount Type</label>
                                            <select name="discount_type" class="form-select" required>
                                                <option value="percentage">Percentage (%)</option>
                                                <option value="fixed">Fixed Amount ($)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Discount Value</label>
                                            <input type="number" name="discount_value" class="form-control" placeholder="e.g. 20" min="0.01" step="0.01" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 fw-semibold mt-auto">
                                            <i class="bi bi-plus-circle me-1"></i>Generate &amp; Send VIP Code
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: Select Customers --}}
                            <div class="col-lg-7">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-people me-2"></i>Send To Customers
                                    <span class="badge bg-success ms-2">{{ $qualifyingCustomers->count() }}</span>
                                </h6>
                                @if ($qualifyingCustomers->count())
                                    <div class="border rounded-3 p-2 vip-scroll-area">
                                        <div class="form-check py-1 px-3 border-bottom vip-border">
                                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="document.querySelectorAll('.customer-check').forEach(c => c.checked = this.checked)">
                                            <label class="form-check-label fw-semibold small text-muted" for="selectAll">Select All</label>
                                        </div>
                                        @foreach ($qualifyingCustomers as $customer)
                                            <div class="form-check py-2 px-3 border-bottom vip-border d-flex align-items-center gap-2">
                                                <input class="form-check-input customer-check" type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" id="cust{{ $customer->id }}" checked>
                                                <label class="form-check-label d-flex w-100 justify-content-between align-items-center" for="cust{{ $customer->id }}">
                                                    <span class="fw-semibold">{{ $customer->name }}</span>
                                                    <span>
                                                        <span class="badge bg-success-subtle text-success-emphasis me-2">{{ $customer->week_orders }}/wk</span>
                                                        <span class="fw-bold vip-text">${{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</span>
                                                    </span>
                                                </label>
                                                <div class="d-flex gap-1 flex-shrink-0">
                                                    <a href="{{ route('admin.users.show', $customer->id) }}" class="btn btn-sm btn-outline-info py-0 px-1" title="View"><i class="bi bi-eye"></i></a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" title="Delete"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-url="{{ route('admin.users.destroy', $customer->id) }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted mt-2 d-block">Uncheck customers you don't want to receive this code.</small>
                                @else
                                    <div class="text-center py-5 text-muted border rounded-3">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No qualifying customers yet.</p>
                                        <small>Customers need at least $500 total spent across all orders.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Generated Codes List --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0">Generated Codes ({{ count($codes) }})</h5>
        </div>
        <div class="card-body p-0">
            @if (count($codes))
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 small fw-bold text-uppercase">Code</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase">Discount</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase">Created</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase">Sent To</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($codes as $entry)
                                <tr>
                                    <td class="px-4 py-3 font-monospace fw-bold" style="letter-spacing: 1px;">{{ $entry['code'] }}</td>
                                    <td class="px-4 py-3">
                                        @if ($entry['discount_type'] === 'percentage')
                                            <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">{{ $entry['discount_value'] }}% off</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2">${{ number_format($entry['discount_value'], 2) }} off</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-muted small">{{ $entry['created_at'] }}</td>
                                    <td class="px-4 py-3">
                                        @if (!empty($entry['sent_to']))
                                            <span class="badge bg-success-subtle text-success-emphasis me-1">{{ $entry['sent_count'] ?? count($entry['sent_to']) }} customers</span>
                                            <button class="btn btn-sm btn-outline-secondary py-0 px-1" data-bs-toggle="tooltip" title="{{ implode(', ', $entry['sent_to']) }}" onclick="alert('Sent to: {{ addslashes(implode(', ', $entry['sent_to'])) }}')">
                                                <i class="bi bi-people"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button class="btn btn-sm btn-outline-success me-1" onclick="navigator.clipboard.writeText('{{ $entry['code'] }}').then(() => { this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 2000); })"><i class="bi bi-clipboard"></i> Copy</button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#vipDeleteModal" data-code="{{ $entry['code'] }}" data-url="{{ route('admin.promotions.vip-codes.delete', $entry['id']) }}"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-ticket fs-1 d-block mb-2"></i>
                    <p class="mb-0">No codes generated yet.</p>
                    <small>Use the form above to generate your first VIP discount code.</small>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="vipDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center vip-danger-circle">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </span>
                </div>
                <h5 class="fw-bold mb-2">Delete VIP Code</h5>
                <p class="text-muted mb-1">Are you sure you want to delete this code?</p>
                <p class="fw-bold font-monospace mb-3" id="vipDeleteCode" style="letter-spacing: 1px;"></p>
                <form id="vipDeleteForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.vip-card-green {
    background: var(--admin-primary-light) !important;
}
.vip-text-dark-green {
    color: var(--admin-primary-dark) !important;
}
.vip-border {
    border-color: var(--admin-border) !important;
}
.vip-text {
    color: var(--admin-text) !important;
}
.vip-scroll-area {
    max-height: 300px;
    overflow-y: auto;
    border-color: var(--admin-border) !important;
    background: var(--admin-surface);
}
.vip-danger-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(220, 38, 38, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('vipDeleteModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('vipDeleteCode').textContent = button.getAttribute('data-code');
            document.getElementById('vipDeleteForm').action = button.getAttribute('data-url');
        });
    }
});
</script>
@endsection
