<footer class="bg-gray-900 text-gray-400">
    <div class="container mx-auto px-4 py-8">

        <div class="flex flex-col lg:flex-row justify-between items-center gap-8 border-b border-gray-700 pb-10 mb-10">

            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-zone Logo" class="h-20">
            </a>

            <div class="w-full lg:w-1/2 max-w-md">
                <div class="flex-col mb-4">
                    <h3 class="text-lg font-semibold text-white mb-2">Join Our Newsletter</h3>
                    <p class="text-sm">Get updates on new products and special offers.</p>
                    <div class="flex items-center gap-2 mt-3">
                        <input type="checkbox" id="newsletter-checkbox"
                            class="w-4 h-4 text-primary bg-gray-800 border-gray-600 rounded focus:ring-primary focus:ring-2">
                        <label for="newsletter-checkbox" class="text-sm text-gray-400">
                            I accept the <a href="{{ route('terms') }}"
                                class="text-primary hover:text-primary-dark transition-colors">Terms & Conditions</a>
                        </label>
                    </div>
                </div>
                <form id="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST" class="flex">
                    @csrf
                    <label for="footer-email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="footer-email" placeholder="Enter your email address"
                        class="w-full bg-gray-800 border-gray-700 text-white rounded-l-md py-3 px-5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button type="submit" aria-label="Subscribe"
                        class="shrink-0 bg-primary text-white rounded-r-md py-3 px-5 font-medium hover:bg-primary-dark transition-colors">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Company</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('showrooms') }}" class="hover:text-white transition-colors">Showrooms</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Shop By</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('brands') }}" class="hover:text-white transition-colors">Brands</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Support</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Useful Links</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms & Conditions</a>
                    </li>
                    <li><a href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">Privacy
                            Policy</a></li>
                    <li><a href="{{ route('shipping-returns') }}" class="hover:text-white transition-colors">Shipping &
                            Returns</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Contact</h4>
                <address class="not-italic space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary shrink-0 mt-1" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 10A7 7 0 103 10c0 2.493 1.698 4.988 3.642 6.574.829.799 1.654 1.381 2.274 1.765.311.193.571.337.757.433.09.046.18.09.28.14l.018.008.006.003c.002.001.003.001.005.002zM10 12a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ $siteContent ->address ?? 'Not available' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary shrink-0" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.129a1.5 1.5 0 01-1.052 1.767l-.12.049c-.11.044-.204.08-.284.113A1.5 1.5 0 004.5 9.879a13.55 13.55 0 006.621 6.621 1.5 1.5 0 001.766-.71l.049-.12a1.5 1.5 0 011.767-1.052l3.129.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15A13.5 13.5 0 011.5 3H2.5A1.5 1.5 0 014 4.5v.001"
                                clip-rule="evenodd" />
                        </svg>
                        <a href="tel:+1234567890" class="hover:text-white transition-colors">{{ $siteContent ->phone ?? 'N/A' }}</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary shrink-0" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                            <path
                                d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                        </svg>
                        <a href="mailto:{{ $siteContent->email ?? 'support@ezone.com' }}"
                            class="hover:text-white transition-colors">{{ $siteContent->email ?? 'support@ezone.com' }}</a>
                    </div>
                </address>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <p>&copy; {{ date('Y') }} E-zone. All rights reserved.</p>

            <div class="flex items-center space-x-5">
                @isset($siteContent->facebook)
                <a href="{{ $siteContent->facebook }}" target="_blank" aria-label="Facebook" class="hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd"
                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.772-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                    clip-rule="evenodd" />
                </svg>
                </a>
                @endisset
                @isset($siteContent->instagram)
                <a href="{{ $siteContent->instagram }}" target="_blank" aria-label="Instagram" class="hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd"
                    d="M12.315 2c2.43 0 2.718.01 3.69.053.972.043 1.633.18 2.19.388.556.208.97.46 1.398.888.428.428.68.842.888 1.398.208.557.345 1.218.388 2.19.043.972.053 1.26.053 3.69s-.01 2.718-.053 3.69c-.043.972-.18 1.633-.388 2.19-.208.556-.46.97-.888 1.398-.428.428-.842.68-1.398.888-.557.208-1.218.345-2.19.388-.972.043-1.26.053-3.69.053s-2.718-.01-3.69-.053c-.972-.043-1.633-.18-2.19-.388-.556-.208-.97-.46-1.398-.888-.428-.428-.68-.842-.888-1.398-.208-.557-.345-1.218-.388-2.19-.043-.972-.053-1.26-.053-3.69s.01-2.718.053-3.69c.043-.972.18-1.633.388-2.19.208-.556.46-.97.888-1.398.428-.428.842-.68 1.398-.888.557-.208 1.218-.345 2.19-.388.972-.043 1.26-.053 3.69-.053zm0-2c-2.463 0-2.776.01-3.743.054-.968.044-1.7.196-2.394.386-.694.19-1.32.48-1.888.988-.568.568-.898 1.19-.988 1.888-.19.694-.342 1.426-.386 2.394C2.56 6.702 2.55 7.016 2.55 9.47s.01 2.768.054 3.736c.044.968.196 1.7.386 2.394.19.694.48 1.32.988 1.888.568.568 1.19.898 1.888.988.694.19 1.426.342 2.394.386.968.044 1.282.054 3.743.054s2.768-.01 3.736-.054c.968-.044 1.7-.196 2.394-.386.694-.19 1.32-.48 1.888-.988.568-.568.898-1.19.988-1.888.19-.694.342-1.426.386-2.394.044-.968.054-1.282.054-3.743s-.01-2.768-.054-3.736c-.044-.968-.196-1.7-.386-2.394-.19-.694-.48-1.32-.988-1.888-.568-.568-1.19-.898-1.888-.988-.694-.19-1.426-.342-2.394-.386C13.298 2.56 12.984 2.55 10.53 2.55zM12 6.865a5.135 5.135 0 100 10.27 5.135 5.135 0 000-10.27zm0 8.27a3.135 3.135 0 110-6.27 3.135 3.135 0 010 6.27zM18.135 5.865a1.162 1.162 0 100 2.324 1.162 1.162 0 000-2.324z"
                    clip-rule="evenodd" />
                </svg>
                </a>
                @endisset
                @isset($siteContent->twitter)
                <a href="{{ $siteContent->twitter }}" target="_blank" aria-label="Twitter" class="hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                </svg>
                </a>
                @endisset
            </div>

            <div class="flex items-center space-x-2">
                <span>We accept:</span>
                <span class="text-white font-bold">VISA</span>
                <span class="text-white font-bold">MasterCard</span>
                <span class="text-white font-bold">PayPal</span>
            </div>
            </div>
        </div>
    </div>
</footer>
