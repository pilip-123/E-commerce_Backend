@php
    $title = $title ?? config('app.name', 'E-Commerce');
    $subtitle = $subtitle ?? 'Admin Panel';
    $titleClass = $titleClass ?? 'fs-6 fw-bold';
    $subtitleClass = $subtitleClass ?? 'text-uppercase small fw-bold';
    $containerClass = $containerClass ?? 'd-flex align-items-center gap-2';
    $logoSizeClass = $logoSizeClass ?? '';
    $showText = $showText ?? true;
@endphp

<a href="{{ route('admin.dashboard') }}" class="{{ $containerClass }} text-decoration-none">
    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
        style="width: 54px; height: 54px; border-radius: 50%; overflow: hidden; background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 3px;">
        <img src="{{ asset('images/logo.png') }}" alt="{{ $title ?? 'Logo' }}"
            style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
    </span>
    @if ($showText)
        <div>
            @if ($title)
                <h1 class="{{ $titleClass }}" style="color: var(--admin-text, #1e293b); line-height: 1.2;">{{ $title }}</h1>
            @endif
            @if ($subtitle)
                <p class="{{ $subtitleClass }}" style="color: var(--admin-primary, #059669); line-height: 1.2; margin: 0;">{{ $subtitle }}
                </p>
            @endif
        </div>
    @endif
</a>
