@extends('client.pages.dashboard-layout')

@section('dashboard-title', statictranslations('My Profile'))

@section('dashboard-content')
    {{-- Profile Header Card --}}
    <div
        class="bg-linear-to-br from-primary/5 via-primary/8 to-primary/10 rounded-2xl shadow-md border border-primary/20 p-6 md:p-8 mb-6 overflow-hidden relative">
        {{-- Decorative Background Elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
        </div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-primary/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2">
        </div>

        <div class="relative z-10">
            {{-- Mobile Layout --}}
            <div class="md:hidden">
                {{-- Avatar Section --}}
                <div class="flex flex-col items-center mb-6">
                    <div class="relative group mb-4">
                        <div
                            class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-xl ring-2 ring-primary/20">
                            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/avatar-placeholder.png') }}"
                                alt="User Avatar" class="w-full h-full object-cover">
                        </div>
                        <button type="button"
                            class="absolute inset-0 bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-white mx-auto mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-xs text-white font-medium">{{ statictranslations('Change') }}</span>
                            </div>
                        </button>
                        {{-- Online Status Badge --}}
                        <div
                            class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-4 border-white rounded-full shadow-lg">
                            <div class="w-full h-full rounded-full bg-green-500 animate-ping opacity-75"></div>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-gray-600 mb-4">{{ auth()->user()->email }}</p>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 justify-center mb-4">
                        @if (auth()->user()->is_premium)
                            <span
                                class="px-4 py-1.5 bg-linear-to-r from-primary to-primary/80 text-white text-xs font-semibold rounded-full shadow-sm">
                                ✨ {{ statictranslations('Premium Member') }}
                            </span>
                        @endif
                        <span
                            class="px-4 py-1.5 bg-white text-gray-700 text-xs font-semibold rounded-full border border-gray-200 shadow-sm">
                            📅 {{ statictranslations('Member since') }} {{ auth()->user()->created_at->format('Y') }}
                        </span>
                    </div>
                </div>

                {{-- Stats Section --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-100 shadow-sm">
                        <p class="text-3xl font-bold text-primary mb-1">{{ $orderCount ?? '0' }}</p>
                        <p class="text-xs text-gray-600 font-medium">{{ statictranslations('Total Orders') }}</p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-100 shadow-sm">
                        <p class="text-3xl font-bold text-primary mb-1">{{ $rewardPoints ?? '0' }}</p>
                        <p class="text-xs text-gray-600 font-medium">{{ statictranslations('Reward Points') }}</p>
                    </div>
                </div>
            </div>

            {{-- Desktop Layout --}}
            <div class="hidden md:flex items-center gap-8">
                {{-- Avatar with Upload Overlay --}}
                <div class="relative group shrink-0">
                    <div
                        class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-xl ring-4 ring-primary/20">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://via.placeholder.com/150x150' }}"
                            alt="User Avatar" class="w-full h-full object-cover">
                    </div>
                    {{-- Online Status Badge --}}
                    <div
                        class="absolute bottom-2 right-2 w-7 h-7 bg-green-500 border-4 border-white rounded-full shadow-lg">
                        <div class="absolute inset-0 rounded-full bg-green-500 animate-ping opacity-75"></div>
                    </div>
                </div>

                {{-- User Info --}}
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ auth()->user()->name }}</h2>
                    <p class="text-base text-gray-600 mb-4">{{ auth()->user()->email }}</p>
                    <div class="flex flex-wrap gap-2">
                        @if (auth()->user()->is_premium)
                            <span
                                class="px-4 py-2 bg-linear-to-r from-primary to-primary/80 text-white text-sm font-semibold rounded-full shadow-md">
                                ✨ Premium Member
                            </span>
                        @endif
                        <span
                            class="px-4 py-2 bg-white text-gray-700 text-sm font-semibold rounded-full border border-gray-200 shadow-sm">
                            📅 Member since {{ auth()->user()->created_at->format('Y') }}
                        </span>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="flex gap-6">
                    <div
                        class="text-center bg-white/80 backdrop-blur-sm rounded-xl p-5 min-w-25 border border-gray-100 shadow-sm">
                        <p class="text-3xl font-bold text-primary mb-1">{{ $orderCount ?? '0' }}</p>
                        <p class="text-sm text-gray-600 font-medium">{{ statictranslations('Orders') }}</p>
                    </div>
                    <div
                        class="text-center bg-white/80 backdrop-blur-sm rounded-xl p-5 min-w-25 border border-gray-100 shadow-sm hidden">
                        <p class="text-3xl font-bold text-primary mb-1">{{ $rewardPoints ?? '0' }}</p>
                        <p class="text-sm text-gray-600 font-medium">{{ statictranslations('Points') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="profileForm" action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Personal Information Section --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 md:p-8 mb-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                    {{ statictranslations('Personal Information') }}
                </h3>
                <button type="button" id="editBtn"
                    class="hidden md:flex items-center gap-2 px-4 py-2 text-sm font-semibold text-primary hover:bg-primary/5 rounded-lg transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ statictranslations('Edit') }}
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                {{-- Name Field --}}
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ statictranslations('Full Name') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" id="name" name="name" required readonly
                            value="{{ old('name', auth()->user()->name) }}"
                            class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 bg-gray-50 rounded-xl focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm md:text-base placeholder:text-gray-400"
                            placeholder="Enter your full name">
                    </div>
                    <div class="error-message text-xs text-red-600 mt-1.5 hidden" data-error-for="name"></div>
                </div>

                {{-- Email Field --}}
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ statictranslations('Email Address') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" disabled required
                            value="{{ old('email', auth()->user()->email) }}"
                            class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 bg-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm md:text-base placeholder:text-gray-400"
                            placeholder="{{ statictranslations('your.email@example.com') }}">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg fill="#6B7280" width="24px" height="24px" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24">
                                <path
                                    d="M17,9V7c0-2.8-2.2-5-5-5S7,4.2,7,7v2c-1.7,0-3,1.3-3,3v7c0,1.7,1.3,3,3,3h10c1.7,0,3-1.3,3-3v-7C20,10.3,18.7,9,17,9z M9,7c0-1.7,1.3-3,3-3s3,1.3,3,3v2H9V7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="error-message text-xs text-red-600 mt-1.5 hidden" data-error-for="email"></div>
                </div>

                {{-- Phone Field --}}
                <div class="group">
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ statictranslations('Phone Number') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="tel" id="phone" name="phone" required readonly
                            value="{{ old('phone', auth()->user()->phone) }}"
                            class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 bg-gray-50 rounded-xl focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm md:text-base placeholder:text-gray-400"
                            placeholder="{{ statictranslations('+1 (555) 000-0000') }}">
                    </div>
                    <div class="error-message text-xs text-red-600 mt-1.5 hidden" data-error-for="phone"></div>
                </div>

                {{-- Avatar Upload Field --}}
                <div class="group">
                    <label for="avatar" class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ statictranslations('Profile Picture') }}
                    </label>
                    <div class="relative">
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" disabled
                            onchange="updateFileName(this)">
                        <label for="avatar" id="avatarLabel"
                            class="flex items-center justify-center w-full px-4 py-3.5 border-2 border-dashed border-gray-300 bg-gray-50 rounded-xl cursor-not-allowed opacity-60 transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-gray-600 group-hover:text-primary transition-colors"
                                    id="file-name">{{ statictranslations('Choose image file') }}</span>
                            </div>
                        </label>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">{{ statictranslations('PNG, JPG, GIF up to 5MB') }}</p>
                    <div class="error-message text-xs text-red-600 mt-1.5 hidden" data-error-for="avatar"></div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div id="actionButtons" class="hidden flex-col-reverse sm:flex-row gap-3 justify-end sticky bottom-4 md:static">
            <button type="button"
                class="px-6 py-3.5 md:py-4 border-2 border-gray-200 bg-white text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-200 text-sm md:text-base"
                onclick="window.location.reload();">
                {{ statictranslations('Cancel') }}
            </button>
            <button type="submit"
                class="px-6 py-3.5 md:py-4 bg-linear-to-r from-primary to-primary/90 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-sm md:text-base flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ statictranslations('Save Changes') }}
            </button>
        </div>
    </form>

    {{-- External JS for profile page --}}
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection
