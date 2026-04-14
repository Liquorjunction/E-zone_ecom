<footer class="bg-gray-900 text-gray-400">
    <div class="container mx-auto px-4 py-8">

        <div class="flex flex-col lg:flex-row justify-between items-center gap-8 border-b border-gray-700 pb-10 mb-10">

            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-zone Logo" class="h-20">
            </a>

            <div class="w-full lg:w-1/2 max-w-md">
                <div class="flex-col mb-4">
                    <h3 class="text-lg font-semibold text-white mb-2">{{ statictranslations('Join Our Newsletter') }}</h3>
                    <p class="text-sm">{{ statictranslations('Get updates on new products and special offers.') }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <input type="checkbox" id="newsletter-checkbox"
                            class="w-4 h-4 text-primary bg-gray-800 border-gray-600 rounded focus:ring-primary focus:ring-2">
                        <label for="newsletter-checkbox" class="text-sm text-gray-400">
                            {{ statictranslations('I accept the') }} <a href="{{ route('terms') }}"
                                class="text-primary hover:text-primary-dark transition-colors">{{ statictranslations('Terms & Conditions') }}</a>
                        </label>
                    </div>
                </div>
                <form id="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST" class="flex">
                    @csrf
                    <label for="footer-email" class="sr-only">{{ statictranslations('Email address') }}</label>
                    <input type="email" name="email" id="footer-email" placeholder="{{ statictranslations('Enter your email address') }}"
                        class="w-full bg-gray-800 border-gray-700 text-white rounded-l-md py-3 px-5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button type="submit" aria-label="{{ statictranslations('Subscribe') }}"
                        class="shrink-0 bg-primary text-white rounded-r-md py-3 px-5 font-medium hover:bg-primary-dark transition-colors">
                        {{ statictranslations('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">{{ statictranslations('Company') }}</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">{{ statictranslations('About Us') }}</a></li>
                    <li><a href="{{ route('showrooms') }}" class="hover:text-white transition-colors">{{ statictranslations('Showrooms') }}</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">{{ statictranslations('Shop By') }}</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('brands') }}" class="hover:text-white transition-colors">{{ statictranslations('Brands') }}</a></li>
                    <li><a href="{{ route('categories') }}" class="hover:text-white transition-colors">{{ statictranslations('Categories') }}</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">{{ statictranslations('Support') }}</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">{{ statictranslations('Contact') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">{{ statictranslations('FAQ') }}</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">{{ statictranslations('Useful Links') }}</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">{{ statictranslations('Terms & Conditions') }}</a>
                    </li>
                    <li><a href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">{{ statictranslations('Privacy Policy') }}</a></li>
                    <li><a href="{{ route('shipping-returns') }}" class="hover:text-white transition-colors">{{ statictranslations('Shipping & Returns') }}</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">{{ statictranslations('Contact') }}</h4>
                <address class="not-italic space-y-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-primary shrink-0 mt-1 text-lg"></i>
                        <span>{{ $siteContent ->address ?? statictranslations('Not available') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-phone text-primary shrink-0 text-lg"></i>
                        <a href="tel:+1234567890" class="hover:text-white transition-colors">{{ $siteContent ->phone ?? statictranslations('N/A') }}</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-envelope text-primary shrink-0 text-lg"></i>
                        <a href="mailto:{{ $siteContent->email ?? 'support@ezone.com' }}"
                            class="hover:text-white transition-colors">{{ $siteContent->email ?? 'support@ezone.com' }}</a>
                    </div>
                </address>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <p>&copy; {{ date('Y') }} E-zone. {{ statictranslations('All rights reserved.') }}</p>

            <div class="flex items-center space-x-5">
                @isset($siteContent->facebook)
                <a href="{{ $siteContent->facebook }}" target="_blank" aria-label="Facebook" class="hover:text-white transition-colors">
                    <i class="fab fa-facebook text-lg"></i>
                </a>
                @endisset
                @isset($siteContent->instagram)
                <a href="{{ $siteContent->instagram }}" target="_blank" aria-label="Instagram" class="hover:text-white transition-colors">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
                @endisset
                @isset($siteContent->twitter)
                <a href="{{ $siteContent->twitter }}" target="_blank" aria-label="Twitter" class="hover:text-white transition-colors">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
                @endisset
            </div>

            <div class="flex items-center space-x-2">
                <span>{{ statictranslations('We accept:') }}</span>
                <span class="text-white font-bold">VISA</span>
                <span class="text-white font-bold">MasterCard</span>
                <span class="text-white font-bold">PayPal</span>
            </div>
            </div>
        </div>
    </div>
</footer>