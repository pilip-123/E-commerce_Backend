@extends('layouts.admin')

@section('title', 'Profile')

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
        Dashboard
      </a>
    </div>
  </div>

  <div class="tabs-wrapper">
    <div class="tabs-nav">
      <button class="tab-btn active" data-tab="profile">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
      </button>
      <button class="tab-btn" data-tab="settings">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        Settings
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
              Introduction
            </h3>
            <p class="card-text">Member since {{ $user->created_at?->format('F Y') ?? 'N/A' }}.</p>
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
                <span>Joined {{ $user->created_at?->format('F Y') }}</span>
              </div>
            </div>
          </div>
        </aside>

        <main>
          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Admin Overview
            </h3>
            <p class="card-text">Welcome back, {{ $user->name }}! Use the sidebar to manage your store.</p>
            <div class="d-flex gap-2 flex-wrap">
              <a href="{{ route('admin.products.index') }}" class="btn-accent btn-accent-sm">Products</a>
              <a href="{{ route('admin.orders.index') }}" class="btn-accent btn-accent-sm">Orders</a>
              <a href="{{ route('admin.customers') }}" class="btn-accent btn-accent-sm">Customers</a>
              <a href="{{ route('admin.reviews.index') }}" class="btn-accent btn-accent-sm">Reviews</a>
            </div>
          </div>
        </main>
      </div>
    </div>

    <div class="tab-content" id="tab-settings">
      <div class="content-layout content-layout--centered">
        <div class="card-custom" style="max-width: 560px;">
          <h3 class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Edit Profile
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
                <a href="#" class="btn-accent-ghost btn-accent-ghost-sm" onclick="document.getElementById('imageInput').click(); return false;">Upload photo</a>
                <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="form-fields">
              <div>
                <label class="field-label">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="field-input @error('name') is-invalid @enderror" required>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="field-input @error('email') is-invalid @enderror" required>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="field-input @error('phone') is-invalid @enderror" placeholder="Optional">
                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">Address</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="field-input @error('address') is-invalid @enderror" placeholder="Optional">
                @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">New Password <span class="text-muted fw-normal">(leave blank to keep current)</span></label>
                <input type="password" name="password" class="field-input @error('password') is-invalid @enderror" placeholder="New password">
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
              <div>
                <label class="field-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="field-input" placeholder="Confirm password">
              </div>
            </div>

            <div class="edit-actions">
              <button type="submit" class="btn-accent">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Save Changes
              </button>
            </div>
          </form>
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
});
</script>
@endsection
