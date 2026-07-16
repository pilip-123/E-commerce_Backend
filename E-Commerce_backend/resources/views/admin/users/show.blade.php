@extends('layouts.admin')

@section('title', $user->name)

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

.btn-outline-accent {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 7px 16px;
  border: 1.5px solid var(--accent);
  border-radius: 999px;
  background: transparent;
  color: var(--accent);
  font-weight: 600;
  font-size: 0.82rem;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s, transform 0.15s;
}

.btn-outline-accent:hover {
  background: var(--accent-soft);
  color: var(--accent);
}

.btn-danger-ghost {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 7px 16px;
  border: 1.5px solid #dc2626;
  border-radius: 999px;
  background: transparent;
  color: #dc2626;
  font-weight: 600;
  font-size: 0.82rem;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s, transform 0.15s;
}

.btn-danger-ghost:hover {
  background: rgba(220, 38, 38, 0.08);
  color: #dc2626;
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

.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.stat-card {
  background: var(--accent-light);
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}

.stat-card .stat-value {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--admin-text);
  margin: 0;
}

.stat-card .stat-label {
  font-size: 0.8rem;
  color: var(--admin-text-muted);
  margin: 4px 0 0;
}

.stat-card .stat-icon {
  color: var(--accent);
  margin-bottom: 8px;
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

  .tabs-wrapper {
    padding: 0 16px;
    overflow-x: auto;
  }

  .tabs-nav {
    width: max-content;
  }

  .stats-grid {
    grid-template-columns: 1fr;
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
      <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-accent btn-accent-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        {{ __('Edit') }}
      </a>
      <a href="{{ route('admin.customers') }}" class="btn-outline-accent">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        {{ __('Back to Users') }}
      </a>
    </div>
  </div>

  <div class="tabs-wrapper">
    <div class="tabs-nav">
      <button class="tab-btn active" data-tab="profile">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        {{ __('Profile') }}
      </button>
      <button class="tab-btn" data-tab="orders">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
        {{ __('Orders') }}
      </button>
      <button class="tab-btn" data-tab="security">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        {{ __('Security') }}
      </button>
    </div>
  </div>

  <div class="profile-content">

    {{-- Profile Tab --}}
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
              <div class="info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span class="text-capitalize">{{ $user->role }}</span>
              </div>
            </div>
          </div>
        </aside>

        <main>
          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
              {{ __('Activity Overview') }}
            </h3>
            <p class="card-text">{{ __('Summary of') }} {{ $user->name }}'s {{ __('activity on the platform.') }}</p>

            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <p class="stat-value">{{ $user->orders_count }}</p>
                <p class="stat-label">{{ __('Orders Placed') }}</p>
              </div>
              <div class="stat-card">
                <div class="stat-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <p class="stat-value">${{ number_format($user->orders_sum_total_amount ?? 0, 2) }}</p>
                <p class="stat-label">{{ __('Total Spent') }}</p>
              </div>
            </div>
          </div>

          <div class="card-custom">
            <h3 class="card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              {{ __('Quick Actions') }}
            </h3>
            <p class="card-text">{{ __('Manage this user from here.') }}</p>
            <div class="d-flex gap-2 flex-wrap">
              <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-accent btn-accent-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                {{ __('Edit Profile') }}
              </a>
              <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}" class="btn-accent-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
                {{ __('View Orders') }}
              </a>
            </div>
          </div>
        </main>
      </div>
    </div>

    {{-- Orders Tab --}}
    <div class="tab-content" id="tab-orders">
      <div class="content-layout content-layout--centered">
        <div class="card-custom">
          <h3 class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
            {{ __('Orders History') }}
            </h3>
            <p class="card-text">{{ __('This user has placed') }} {{ $user->orders_count }} {{ __('order(s) totaling') }} ${{ number_format($user->orders_sum_total_amount ?? 0, 2) }}.</p>
          <div class="d-flex justify-content-center pt-2">
            <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}" class="btn-accent">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              {{ __('View All Orders') }}
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Security Tab --}}
    <div class="tab-content" id="tab-security">
      <div class="content-layout content-layout--centered">
        <div class="card-custom">
          <h3 class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            {{ __('Account Security') }}
            </h3>
            <p class="card-text">{{ __('Security information for this account.') }}</p>
          <div class="info-rows">
            <div class="info-row">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <span>{{ __('Email Verified:') }} {{ $user->email_verified_at ? __('Yes') : __('No') }}</span>
            </div>
            <div class="info-row">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <span>{{ __('Registered:') }} {{ $user->created_at?->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="info-row">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <span>{{ __('Last Updated:') }} {{ $user->updated_at?->format('M d, Y \a\t h:i A') }}</span>
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
});
</script>
@endsection
