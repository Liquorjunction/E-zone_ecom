@if (!empty($returningGuest) && !Auth::check())
    <div id="returningGuestBanner"
        class="fixed bottom-6 left-6 z-40 max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 p-5 hidden transition-all duration-300 transform translate-y-4 rtl:left-auto rtl:right-6">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-sm font-bold text-gray-900">{{ statictranslations('Welcome back!') }}</h4>
                    <button type="button" id="dismissReturningGuestBanner"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                        aria-label="{{ statictranslations('Close') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-600 mb-3 leading-relaxed">
                    {{ statictranslations('Create an account with your email to track your past orders, manage saved addresses, and enjoy faster checkout.') }}
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('register') }}"
                        class="px-3.5 py-1.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                        {{ statictranslations('Create Account') }}
                    </a>
                    <button type="button" id="dismissReturningGuestLink"
                        class="text-xs text-gray-500 hover:text-gray-700 transition-colors font-medium">
                        {{ statictranslations('Maybe later') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var banner = document.getElementById('returningGuestBanner');
            if (!banner) return;
            if (sessionStorage.getItem('ezone_returning_guest_dismissed') === '1') {
                return;
            }

            setTimeout(function() {
                banner.classList.remove('hidden');
                setTimeout(function() {
                    banner.classList.remove('translate-y-4');
                    banner.classList.add('translate-y-0');
                }, 50);
            }, 1200);

            function dismissBanner() {
                banner.classList.add('translate-y-4', 'opacity-0');
                setTimeout(function() {
                    banner.remove();
                }, 300);
                sessionStorage.setItem('ezone_returning_guest_dismissed', '1');
            }

            var closeBtn = document.getElementById('dismissReturningGuestBanner');
            var dismissLink = document.getElementById('dismissReturningGuestLink');
            if (closeBtn) closeBtn.addEventListener('click', dismissBanner);
            if (dismissLink) dismissLink.addEventListener('click', dismissBanner);
        });
    </script>
@endif
