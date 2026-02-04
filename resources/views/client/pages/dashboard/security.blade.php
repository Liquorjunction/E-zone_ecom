@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'Security')

@section('dashboard-content')
    {{-- Change Password Section --}}
    <form action="{{ route('dashboard.security.update-password') }}" method="POST"
        class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
        @csrf
        <div class="flex items-center justify-between mb-4 lg:mb-6">
            <h3 class="text-lg lg:text-xl font-bold text-gray-900 flex items-center">
                <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
                Change Password
            </h3>
            <span class="text-xs text-gray-500">Last updated: {{ $lastPasswordUpdate ? \Carbon\Carbon::parse($lastPasswordUpdate)->diffForHumans() : 'Never' }}</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full pl-12 pr-12 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
                        placeholder="Enter your current password">
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center js-toggle-password"
                        data-target="current_password">
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" id="new_password" name="new_password" required
                        class="w-full pl-12 pr-12 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
                        placeholder="Enter your new password">
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center js-toggle-password"
                        data-target="new_password">
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                {{-- Password Requirements --}}
                <div class="mt-3 space-y-2">
                    <p class="text-xs font-semibold text-gray-700">Password must contain:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="length">
                            <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="js-pwd-text">At least 8 characters</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="uppercase">
                            <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="js-pwd-text">One uppercase letter</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="number">
                            <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="js-pwd-text">One number</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600 js-pwd-rule" data-rule="special">
                            <svg class="w-4 h-4 js-pwd-icon text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <input type="password" id="confirm_password" name="new_password_confirmation" required
                        class="w-full pl-12 pr-12 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
                        placeholder="Confirm your new password">
                    <p class="js-confirm-msg text-xs mt-1"></p>
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center js-toggle-password"
                        data-target="confirm_password">
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6 lg:mt-8 pt-4 lg:pt-6 border-t-2 border-gray-100 flex justify-end">
            <button type="submit" id="pwd-submit"
                class="px-6 py-3 lg:py-4 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary/90 hover:shadow-xl transition-all text-sm lg:text-base flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Password
            </button>
        </div>
    </form>
@endsection
