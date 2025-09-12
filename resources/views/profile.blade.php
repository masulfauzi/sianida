@extends('layouts.app') <!-- or your main layout -->

@section('main')
<div class="container py-4">
  <div class="row g-4">
    <!-- Left: User Information -->
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <!-- Avatar (optional) -->
          @php
            // If you have an avatar URL stored on the user model, use it here.
            // Otherwise, you can show a Gravatar or a placeholder image.
            $avatarUrl = $user->avatar_url ?? null;
          @endphp

          <img src="{{ $avatarUrl ?? 'https://via.placeholder.com/150' }}" alt="Avatar" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
          <h4 class="card-title mb-1">{{ $user->name ?? 'User' }}</h4>
          <p class="text-muted mb-3">{{ $user->email }}</p>

          <!-- Optional extra info -->
          <ul class="list-unstyled text-start mt-3">
            <li><strong>Joined</strong>: {{ $user->created_at->format('F j, Y') }}</li>
            @if (isset($user->profile_title))
              <li><strong>Title</strong>: {{ $user->profile_title }}</li>
            @endif
          </ul>
        </div>
      </div>
    </div>

    <!-- Right: Change Password Form -->
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title mb-4">Account Security</h5>

          <!-- Success message -->
          @if (session('status'))
            <div class="alert alert-success" role="alert">
              {{ session('status') }}
            </div>
          @endif

          <!-- Global validation errors -->
          @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('profile.change-password') }}">
            @csrf

            <div class="mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <input
                id="current_password"
                name="current_password"
                type="password"
                class="form-control @error('current_password') is-invalid @enderror"
                required
                autocomplete="current-password"
              >
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">New Password</label>
              <input
                id="password"
                name="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                required
                autocomplete="new-password"
              >
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm New Password</label>
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control"
                required
                autocomplete="new-password"
              >
            </div>

            <button type="submit" class="btn btn-primary">Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection