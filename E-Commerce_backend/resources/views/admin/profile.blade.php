@extends('layouts.admin')

@section('title', __('Profile'))

@section('content')
<style>
.profile-page {
  --accent: #16a34a;
  --accent-soft: rgba(34, 197, 94, 0.08);
  --accent-light: #f0fdf4;
  --bg: #f8fff9;
  --radius: 16px;
  --shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  max-width: 1100px;
  margin: 0 auto;
}

[data-theme="dark"] .profile-page {
  --accent: #34d399;
  --accent-soft: rgba(52, 211, 153, 0.12);
  --accent-light: rgba(52, 211, 153, 0.06);
  --bg: #0f172a;
  --shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
}

.cover-banner {
  position: relative;
  height: 200px;
  border-radius: var(--radius) var(--radius) 0 0;
  overflow: hidden;
  background: linear-gradient(135deg, #bbf7d0 0%, #86efac 50%, #4ade80 100%);
}

.profile-bar {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--admin-surface);
  padding: 0 32px 20px;
  border-radius: 0 0 var(--radius) var(--radius);
  box-shadow: var(--shadow);
}

.profile-bar__avatar {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: -48px;
  flex: 1;
}

.avatar-frame {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  border: 4px solid var(--admin-surface);
  overflow: hidden;
  position: relative;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--accent), #22c55e);
  color: #fff;
  font-size: 2rem;
  font-weight: 700;
}

.profile-name {
  margin: 8px 0 2px;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--admin-text);
}

.profile-role {
  margin: 0;
  font-size: 0.85rem;
  color: var(--admin-text-muted);
}

.profile-bar__actions {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 280px;
  justify-content: flex-end;
}

.btn-accent {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 22px;
  border: 0;
  border-radius: 999px;
  background: var(--accent);
  color: #fff;
  font-weight: 600;
  font-size: 0.88rem;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s, transform 0.15s;
}

.btn-accent:hover {
  opacity: 0.92;
  transform: translateY(-1px);
  color: #fff;
}

.btn-accent-sm {
  padding: 7px 16px;
  font-size: 0.82rem;
}

.btn-accent-ghost {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border: 0;
  border-radius: 999px;
  background: transparent;
  color: var(--accent);
  font-weight: 500;
  font-size: 0.85rem;
  cursor: pointer;
  transition: background 0.2s;
  text-decoration: none;
}

.btn-accent-ghost:hover {
  background: var(--accent-soft);
  color: var(--accent);
}

.tabs-wrapper {
  background: var(--accent-light);
  border-radius: var(--radius);
  margin-top: 16px;
  padding: 0 32px;
}

.tabs-nav {
  display: flex;
  gap: 4px;
}

.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 12px 18px;
  border: 0;
  background: transparent;
  color: var(--admin-text-muted);
  font-size: 0.88rem;
  font-weight: 500;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: all 0.2s;
}

.tab-btn:hover {
  color: var(--accent);
  background: rgba(34, 197, 94, 0.04);
}

.tab-btn.active {
  color: var(--accent);
  border-bottom-color: var(--accent);
  font-weight: 600;
}

.profile-content {
  margin-top: 20px;
}

.content-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 24px;
  align-items: start;
}

.content-layout--centered {
  grid-template-columns: 1fr;
  max-width: 600px;
  margin: 0 auto;
}

.card-custom {
  background: var(--admin-surface);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 24px;
  margin-bottom: 20px;
  border: 0;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0 0 12px;
  font-size: 1rem;
  font-weight: 700;
  color: var(--admin-text);
}

.card-title svg {
  color: var(--accent);
  flex: none;
}

.card-text {
  margin: 0 0 16px;
  color: var(--admin-text-muted);
  font-size: 0.9rem;
  line-height: 1.6;
}

.info-rows {
  display: grid;
  gap: 12px;
}

.info-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.88rem;
  color: var(--admin-text-muted);
}

.info-row svg {
  flex: none;
  color: var(--accent);
  opacity: 0.7;
}

.form-fields {
  display: grid;
  gap: 14px;
}

.field-label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--admin-text);
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid var(--admin-border);
  border-radius: 10px;
  font-size: 0.9rem;
  outline: none;
  background: var(--admin-surface);
  color: var(--admin-text);
  transition: border-color 0.2s;
}

.field-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-soft);
}

.field-input.is-invalid {
  border-color: #dc2626;
}

.field-input:disabled {
  background: var(--admin-bg);
  color: var(--admin-text-muted);
}

.edit-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.form-success {
  margin: 12px 0 0;
  padding: 10px 14px;
  border-radius: 10px;
  background: var(--accent-soft);
  color: var(--accent);
  font-size: 0.85rem;
}

.form-error {
  margin: 12px 0 0;
  padding: 10px 14px;
  border-radius: 10px;
  background: rgba(220, 38, 38, 0.08);
  color: #dc2626;
  font-size: 0.85rem;
}

.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

.avatar-upload-row {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}

.avatar-upload-frame {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  overflow: hidden;
  position: relative;
  cursor: pointer;
  border: 2px solid var(--accent-light);
}

.avatar-upload-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.avatar-upload-frame .avatar-placeholder-sm {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--accent), #22c55e);
  color: #fff;
  font-weight: 700;
  font-size: 1.3rem;
}

/* ─── Settings Grid ─── */
.settings-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  align-items: start;
}

@media (max-width: 860px) {
  .settings-grid {
    grid-template-columns: 1fr;
  }
}

/* ─── Toggle Switch ─── */
.toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 12px 0;
  border-bottom: 1px solid var(--admin-border);
}

.toggle-row:last-child {
  border-bottom: none;
}

.toggle-info {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  min-width: 0;
}

.toggle-info svg {
  flex-shrink: 0;
  color: var(--accent);
  margin-top: 2px;
}

.toggle-info > div {
  display: grid;
  gap: 2px;
}

.toggle-info strong {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--admin-text);
}

.toggle-info span {
  font-size: 0.78rem;
  color: var(--admin-text-muted);
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 22px;
  flex-shrink: 0;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background: #d1d5db;
  border-radius: 999px;
  transition: background 0.2s;
}

.toggle-slider::before {
  content: "";
  position: absolute;
  left: 3px;
  bottom: 3px;
  width: 16px;
  height: 16px;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.2s;
}

.toggle-switch input:checked + .toggle-slider {
  background: var(--accent);
}

.toggle-switch input:checked + .toggle-slider::before {
  transform: translateX(18px);
}

/* ─── Theme Options ─── */
.theme-options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.theme-option {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 18px;
  border: 2px solid var(--admin-border);
  border-radius: 12px;
  background: var(--admin-surface);
  cursor: pointer;
  transition: all 0.2s;
}

.theme-option:hover {
  border-color: var(--accent);
  background: var(--accent-soft);
}

.theme-option.active {
  border-color: var(--accent);
  background: var(--accent-soft);
}

.theme-option svg {
  color: var(--accent);
}

.theme-option strong {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--admin-text);
}

@media (max-width: 860px) {
  .profile-bar {
    flex-direction: column;
    padding: 0 20px 20px;
    gap: 12px;
  }

  .profile-bar__avatar {
    margin-top: -48px;
  }

  .profile-bar__actions {
    min-width: unset;
    justify-content: center;
  }

  .content-layout {
    grid-template-columns: 1fr;
  }

  .content-layout--centered {
    max-width: 100%;
  }

  .tabs-wrapper {
    padding: 0 16px;
    overflow-x: auto;
  }

  .tabs-nav {
    width: max-content;
  }
}
</style>

<div class="profile-page">
  <div class="cover-banner"></div>

  <div class="profile-bar">
    <div class="profile-bar__avatar">
      <div class="avatar-frame">
        @if ($user->image_url)
          <img src="{{ asset('storage/' . $user->image_url) }}" alt="{{ $user->name }}" class="avatar-img">
        @else
          <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        @endif
      </div>
      <h1 class="profile-name">{{ $user->name }}</h1>
      <p class="profile-role">{{ ucfirst($user->role) }}</p>
    </div>

    <div class="profile-bar__actions">
      <a href="{{ route('admin.dashboard') }}" class="btn-accent btn-accent-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        {{ __('Dashboard') }}
      </a>
    </div>
  </div>

  <div class="tabs-wrapper">
    <div class="tabs-nav">
      <button class="tab-btn active" data-tab="profile">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        {{ __('Profile') }}
      </button>
      <button class="tab-btn" data-tab="settings">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        {{ __('Settings') }}
      </button>
    </div>
  </div>

  <div class="profile-content">

    <div class="tab-content active" id="tab-profile">
      <div class="content-layout">
        <aside>
          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              {{ __('Introduction') }}
            </h3>
            <p class="card-text">{{ __('Member since') }} {{ $user->created_at?->format('F Y') ?? __('N/A') }}.</p>
            <div class="info-rows">
              <div class="info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                <span>{{ $user->email }}</span>
              </div>
              @if($user->phone)
              <div class="info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span>{{ $user->phone }}</span>
              </div>
              @endif
              @if($user->address)
              <div class="info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>{{ $user->address }}</span>
              </div>
              @endif
              <div class="info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ __('Joined') }} {{ $user->created_at?->format('F Y') }}</span>
              </div>
            </div>
          </div>
        </aside>

        <main>
          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              {{ __('Admin Overview') }}
            </h3>
            <p class="card-text">{{ __('Welcome back') }}, {{ $user->name }}! {{ __('Use the sidebar to manage your store.') }}</p>
            <div class="d-flex gap-2 flex-wrap">
              <a href="{{ route('admin.products.index') }}" class="btn-accent btn-accent-sm">{{ __('Products') }}</a>
              <a href="{{ route('admin.orders.index') }}" class="btn-accent btn-accent-sm">{{ __('Orders') }}</a>
              <a href="{{ route('admin.customers') }}" class="btn-accent btn-accent-sm">{{ __('Customers') }}</a>
              <a href="{{ route('admin.reviews.index') }}" class="btn-accent btn-accent-sm">{{ __('Reviews') }}</a>
            </div>
          </div>
        </main>
      </div>
    </div>

    <div class="tab-content" id="tab-settings">
      <div class="settings-grid">
        {{-- Edit Profile --}}
        <div class="card-custom">
          <h3 class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            {{ __('Edit Profile') }}
          </h3>

          @if (session('status'))
            <div class="form-success">{{ session('status') }}</div>
          @endif

          @if (session('error'))
            <div class="form-error">{{ session('error') }}</div>
          @endif

          <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="avatar-upload-row">
              <div class="avatar-upload-frame">
                @if ($user->image_url)
                  <img src="{{ asset('storage/' . $user->image_url) }}" alt="Avatar">
                @else
                  <div class="avatar-placeholder-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
              </div>
              <div>
                <a href="#" class="btn-accent-ghost btn-accent-ghost-sm" onclick="document.getElementById('imageInput').click(); return false;">{{ __('Upload photo') }}</a>
                <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="form-fields">
              <div>
                <label class="field-label">{{ __('Full Name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="field-input @error('name') is-invalid @enderror" required>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="field-input @error('email') is-invalid @enderror" required>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="field-input @error('phone') is-invalid @enderror" placeholder="{{ __('Optional') }}">
                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">{{ __('Address') }}</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="field-input @error('address') is-invalid @enderror" placeholder="{{ __('Optional') }}">
                @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">{{ __('New Password') }} <span class="text-muted fw-normal">{{ __('(leave blank to keep current)') }}</span></label>
                <div class="position-relative">
                  <input type="password" name="password" id="admin-new-password" class="field-input @error('password') is-invalid @enderror" placeholder="{{ __('New password') }}" style="padding-right: 40px;">
                  <button type="button" onclick="togglePwd('admin-new-password', this)" tabindex="-1"
                    class="position-absolute top-50 end-0 translate-middle-y btn border-0 d-flex align-items-center text-muted" style="padding: 8px 12px;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                </div>
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">{{ __('Confirm Password') }}</label>
                <div class="position-relative">
                  <input type="password" name="password_confirmation" id="admin-confirm-password" class="field-input" placeholder="{{ __('Confirm password') }}" style="padding-right: 40px;">
                  <button type="button" onclick="togglePwd('admin-confirm-password', this)" tabindex="-1"
                    class="position-absolute top-50 end-0 translate-middle-y btn border-0 d-flex align-items-center text-muted" style="padding: 8px 12px;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="edit-actions">
              <button type="submit" class="btn-accent">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                {{ __('Save Changes') }}
              </button>
            </div>
          </form>
        </div>

        <div>
          {{-- Notification Controls --}}
          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
              {{ __('Notification Preferences') }}
            </h3>
            <p class="card-text">{{ __('Choose which notifications you want to receive.') }}</p>

            @php $userPrefs = $user->notification_preferences ?? []; @endphp
            <div class="form-fields">
              @php
                $notifPrefs = [
                  'order_updates' => ['label' => __('Order Updates'), 'desc' => __('Get notified when orders are placed or status changes'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                  'new_promotions' => ['label' => __('New Promotions'), 'desc' => __('Receive alerts when promotions are created'), 'icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7'],
                  'low_stock' => ['label' => __('Low Stock Alerts'), 'desc' => __('Get warned when products run low on stock'), 'icon' => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                  'new_products' => ['label' => __('New Products'), 'desc' => __('Notifications when new products are added'), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                  'review_alerts' => ['label' => __('Review Alerts'), 'desc' => __('Get notified when customers leave reviews'), 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                ];
              @endphp
              @foreach ($notifPrefs as $key => $pref)
                <div class="toggle-row">
                  <div class="toggle-info">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $pref['icon'] }}"/></svg>
                    <div>
                      <strong>{{ $pref['label'] }}</strong>
                      <span>{{ $pref['desc'] }}</span>
                    </div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" class="notif-toggle" data-key="{{ $key }}" {{ ($userPrefs[$key] ?? true) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              @endforeach
              <div id="notifFeedback" class="form-success d-none" style="margin:8px 0 0;padding:6px 12px;font-size:0.8rem;">{{ __('Saved') }}</div>
            </div>
          </div>

          {{-- Theme Controls --}}
          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
              {{ __('Theme') }}
            </h3>
            <p class="card-text">{{ __('Switch between light and dark mode.') }}</p>

            <div class="theme-options">
              <button class="theme-option active" onclick="setTheme('light')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <strong>{{ __('Light') }}</strong>
              </button>
              <button class="theme-option" onclick="setTheme('dark')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <strong>{{ __('Dark') }}</strong>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const tabBtns = document.querySelectorAll('.tab-btn');
  tabBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      tabBtns.forEach(function(b) { b.classList.remove('active'); });
      document.querySelectorAll('.tab-content').forEach(function(tc) { tc.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById('tab-' + btn.getAttribute('data-tab')).classList.add('active');
    });
  });

  // Theme toggle — sync with layout
  window.setTheme = function(theme) {
    document.querySelectorAll('.theme-option').forEach(function(btn) {
      btn.classList.toggle('active', btn.querySelector('strong')?.textContent.toLowerCase() === theme);
    });
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('admin_theme', theme);
    var icon = document.getElementById('themeIcon');
    if (icon) {
      icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
  };

  // Notification toggles — save via AJAX
  document.querySelectorAll('.notif-toggle').forEach(function(cb) {
    cb.addEventListener('change', function() {
      var key = this.getAttribute('data-key');
      var val = this.checked;
      var formData = new FormData();
      formData.append('key', key);
      formData.append('value', val ? '1' : '0');
      fetch('{{ route('admin.notifications.preferences') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData,
      }).then(function(r) { return r.json(); }).then(function(data) {
        var fb = document.getElementById('notifFeedback');
        if (data.success) {
          fb.textContent = '{{ __("Preference saved") }}';
          fb.className = 'form-success';
        } else {
          fb.textContent = data.message || '{{ __("Error saving preference") }}';
          fb.className = 'form-error';
        }
        fb.classList.remove('d-none');
        setTimeout(function() { fb.classList.add('d-none'); }, 2500);
      });
    });
  });

  var initialTheme = localStorage.getItem('admin_theme');
  if (!initialTheme) {
    initialTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }
  setTheme(initialTheme);
});
</script>
@endsection
