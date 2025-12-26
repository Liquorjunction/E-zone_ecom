@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'Security')

@section('dashboard-content')
  {{-- Security Overview Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
    {{-- Password Strength --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600">Password Strength</p>
          <p class="text-lg font-bold text-gray-900">Strong</p>
        </div>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
      </div>
    </div>

    {{-- (2FA removed) --}}

    {{-- Active Sessions --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600">Active Sessions</p>
          <p class="text-lg font-bold text-gray-900">{{ count($sessions ?? []) }} Device{{ count($sessions ?? []) !== 1 ? 's' : '' }}</p>
        </div>
      </div>
      <a href="#sessions" class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
        Manage →
      </a>
    </div>
  </div>

  {{-- Change Password Section --}}
  <form action="{{ route('dashboard.security.update-password') }}" method="POST" class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
    @csrf
    <div class="flex items-center justify-between mb-4 lg:mb-6">
      <h3 class="text-lg lg:text-xl font-bold text-gray-900 flex items-center">
        <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
        Change Password
      </h3>
      <span class="text-xs text-gray-500">Last changed 3 months ago</span>
    </div>

    <div class="max-w-2xl space-y-4 lg:space-y-5">
      {{-- Current Password --}}
      <div>
        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
          Current Password *
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input
            type="password"
            id="current_password"
            name="current_password"
            required
            class="w-full pl-12 pr-12 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
            placeholder="Enter your current password"
          >
          <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center js-toggle-password" data-target="current_password">
            <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      {{-- New Password --}}
      <div>
        <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
          New Password *
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input
            type="password"
            id="new_password"
            name="new_password"
            required
            class="w-full pl-12 pr-12 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
            placeholder="Enter your new password"
          >
          <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center js-toggle-password" data-target="new_password">
            <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>

        {{-- Password Requirements --}}
        <div class="mt-3 space-y-2">
          <p class="text-xs font-semibold text-gray-700">Password must contain:</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="length">
              <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="js-pwd-text">At least 8 characters</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="uppercase">
              <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="js-pwd-text">One uppercase letter</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="number">
              <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="js-pwd-text">One number</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="special">
              <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="js-pwd-text">One special character</span>
            </div>
          </div>
        </div>

        {{-- Strength Indicator --}}
        <div id="pwd-strength" class="mt-3 hidden">
          <p class="text-xs font-semibold text-gray-700 mb-2">Strength</p>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="pwd-strength-bar" class="h-2 rounded-full bg-red-500" style="width:10%"></div>
          </div>
          <div class="mt-1 text-xs text-gray-600" id="pwd-strength-text">Very weak</div>
        </div>
      </div>

      {{-- Confirm Password --}}
      <div>
        <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">
          Confirm New Password *
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <input
            type="password"
            id="confirm_password"
            name="new_password_confirmation"
            required
            class="w-full pl-12 pr-12 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
            placeholder="Confirm your new password"
          >
          <p class="js-confirm-msg text-xs mt-1"></p>
          <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center js-toggle-password" data-target="confirm_password">
            <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div class="mt-6 lg:mt-8 pt-4 lg:pt-6 border-t-2 border-gray-100 flex justify-end">
      <button
        type="submit"
        id="pwd-submit"
        class="px-6 py-3 lg:py-4 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary/90 hover:shadow-xl transition-all text-sm lg:text-base flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Update Password
      </button>
    </div>
  </form>

  {{-- Two-factor authentication removed from this installation --}}

  {{-- Active Sessions Section --}}
  <div id="sessions" class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
    <div class="flex items-center justify-between mb-4 lg:mb-6">
      <h3 class="text-lg lg:text-xl font-bold text-gray-900 flex items-center">
        <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
        Active Sessions
      </h3>
      <form action="{{ route('dashboard.security.logout-all') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-600 transition-colors">Log Out All</button>
      </form>
    </div>
    <div class="space-y-3 lg:space-y-4">
      @if(!empty($sessions) && count($sessions) > 0)
        @foreach($sessions as $s)
          @php
            $ua = $s->user_agent ?? 'Unknown device';
            $ip = $s->ip_address ?? null;
            $last = \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans();
            $isCurrent = session()->getId() === $s->id;
          @endphp

          <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 border-2 rounded-xl {{ $isCurrent ? 'bg-gradient-to-r from-green-50 to-transparent border-green-100' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-start gap-4 flex-1">
              <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm lg:text-base mb-1">{{ \Illuminate\Support\Str::limit($ua, 50) }}</p>
                @if($ip)
                  <p class="text-xs lg:text-sm text-gray-600">{{ $ip }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">Last active {{ $last }}</p>
              </div>
            </div>
            @if(!$isCurrent)
              <form action="{{ route('dashboard.security.revoke-session') }}" method="POST" class="self-start sm:self-center">
                @csrf
                <input type="hidden" name="session_id" value="{{ $s->id }}">
                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-600 transition-colors whitespace-nowrap">Revoke</button>
              </form>
            @else
              <span class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Current</span>
            @endif
          </div>
        @endforeach
      @else
        <p class="text-sm text-gray-600">No active sessions found for your account.</p>
      @endif
    </div>

    {{-- Info Box --}}
    <div class="mt-6 p-4 bg-blue-50 border-2 border-blue-100 rounded-xl">
      <div class="flex gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div>
          <p class="text-sm font-semibold text-blue-900 mb-1">Security Tip</p>
          <p class="text-xs text-blue-800">If you see any suspicious activity or unfamiliar devices, revoke access immediately and change your password.</p>
        </div>
      </div>
    </div>
  </div>
@endsection
