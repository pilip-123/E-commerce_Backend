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
    <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white flex-shrink-0"
          style="width: 38px; height: 38px; background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 2px 8px rgba(5,150,105,0.3);">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
    </span>
    @if ($showText)
    <div>
        @if ($title)
        <h1 class="{{ $titleClass }}" style="color: #1e293b; line-height: 1.2;">{{ $title }}</h1>
        @endif
        @if ($subtitle)
        <p class="{{ $subtitleClass }}" style="color: #059669; line-height: 1.2; margin: 0;">{{ $subtitle }}</p>
        @endif
    </div>
    @endif
</a>
