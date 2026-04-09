@php
    // Determine section active states
    $isProductSectionActive =
        request()->routeIs('admin.brands.*') ||
        request()->routeIs('admin.category-subcategory.categories.*') ||
        request()->routeIs('admin.category-subcategory.subcategories.*') ||
        request()->routeIs('admin.products.*');

    $isInventorySectionActive =
        request()->routeIs('admin.stores.*') ||
        request()->routeIs('admin.warehouses.*') ||
        request()->routeIs('admin.inventory.*') ||
        request()->routeIs('admin.inventory-transactions.*');

    $isCustomerSectionActive = request()->routeIs('admin.customers.*');

    $isHomeMgmtActive =
        request()->routeIs('admin.hero-slider.*') ||
        request()->routeIs('admin.cards.*') ||
        request()->routeIs('admin.quick-stats.*') ||
        request()->routeIs('admin.whyshopwithus.*');

    $isCMSActive =
        request()->routeIs('admin.about_us.*') ||
        request()->routeIs('admin.showrooms.*') ||
        request()->routeIs('admin.privacy-policy.*') ||
        request()->routeIs('admin.tncs.*') ||
        request()->routeIs('admin.shipping-return.*');

    $isFAQActive = request()->routeIs('admin.faq-categories.*') || request()->routeIs('admin.faqs.*');

    $isPagesSectionActive = $isHomeMgmtActive || $isCMSActive || $isFAQActive;

    $isUserSectionActive =
        request()->routeIs('admin.user-management.*') || request()->routeIs('admin.roles-permissions.*');

    $isOrdersSectionActive = request()->routeIs('admin.orders.*');

    $isReviewsSectionActive = request()->routeIs('admin.reviews.*');

    $isReportsSectionActive = request()->routeIs('admin.reports.*');

    $isCouponSectionActive = request()->routeIs('admin.coupons.*') || request()->routeIs('admin.coupon-usage.*');

    $isDiscountSectionActive = request()->routeIs('admin.discounts.*');
    $isNewsletterSectionActive = request()->routeIs('admin.newsletter-subscribers.*');
    $isContactUsSectionActive = request()->routeIs('admin.contact-us.*');
@endphp

<aside class="sidebar" id="adminSidebar" role="navigation" aria-label="Admin Sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                        style="height: 120px; width: 120px; object-fit: contain;" />
                </div>
                <div class="logo-mini">
                    <img src="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" alt="E-Zone Mini Logo"
                        style="height: 50px; width: 50px; object-fit: contain;" />
                </div>
            </div>
        </a>

        <button class="sidebar-pin-btn" id="sidebarPinBtn" title="Pin Sidebar" aria-label="Pin Sidebar">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 12V4H8V12H6V14H11V21H13V14H18V12H16Z" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </svg>
        </button>
    </div>

    <div class="sidebar-body" style="overflow-y: auto; max-height: calc(100vh - 120px);">
        <div class="sidebar-list">
            <ul class="navbar-nav" id="sidebar-menu">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="icon">
                            <i class="bi bi-grid"></i>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                <li>
                    <hr class="hr-horizontal">
                </li>

                <!-- Product Management -->
                @canany(['brands.read', 'category.read', 'subcategory.read', 'products.read'])
                    <li class="nav-item">
                        <a class="nav-link {{ $isProductSectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-products" role="button"
                            aria-expanded="{{ $isProductSectionActive ? 'true' : 'false' }}"
                            aria-controls="sidebar-products">
                            <i class="icon">
                                <i class="bi bi-box-seam"></i>
                            </i>
                            <span class="item-name">Product Management</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-right"></i>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $isProductSectionActive ? 'show' : '' }}" id="sidebar-products"
                            data-bs-parent="#sidebar-menu">
                            @can('brands.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                                        href="{{ route('admin.brands.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-tags"></i>
                                        </i>
                                        <span class="item-name">Brands</span>
                                    </a>
                                </li>
                            @endcan

                            @can('category.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.category-subcategory.categories.*') ? 'active' : '' }}"
                                        href="{{ route('admin.category-subcategory.categories.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-grid-3x3"></i>
                                        </i>
                                        <span class="item-name">Categories</span>
                                    </a>
                                </li>
                            @endcan

                            @can('subcategory.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.category-subcategory.subcategories.*') ? 'active' : '' }}"
                                        href="{{ route('admin.category-subcategory.subcategories.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-diagram-3"></i>
                                        </i>
                                        <span class="item-name">Subcategories</span>
                                    </a>
                                </li>
                            @endcan

                            @can('products.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                                        href="{{ route('admin.products.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-box"></i>
                                        </i>
                                        <span class="item-name">Products</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcanany

                <!-- Inventory -->
                @canany(['stores.read', 'warehouses.read', 'inventories.read', 'inventory-transaction.read'])
                    <li class="nav-item">
                        <a class="nav-link {{ $isInventorySectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-inventory" role="button"
                            aria-expanded="{{ $isInventorySectionActive ? 'true' : 'false' }}"
                            aria-controls="sidebar-inventory">
                            <i class="icon">
                                <i class="bi bi-boxes"></i>
                            </i>
                            <span class="item-name">Inventory</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-right"></i>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $isInventorySectionActive ? 'show' : '' }}" id="sidebar-inventory"
                            data-bs-parent="#sidebar-menu">
                            @can('inventories.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                                        href="{{ route('admin.inventory.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-archive"></i>
                                        </i>
                                        <span class="item-name">Inventory</span>
                                    </a>
                                </li>
                            @endcan

                            @can('inventory-transaction.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.inventory-transactions.*') ? 'active' : '' }}"
                                        href="{{ route('admin.inventory-transactions.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </i>
                                        <span class="item-name">Inventory Transactions</span>
                                    </a>
                                </li>
                            @endcan

                            @can('store.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}"
                                        href="{{ route('admin.stores.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-shop"></i>
                                        </i>
                                        <span class="item-name">Stores</span>
                                    </a>
                                </li>
                            @endcan

                            @can('warehouse.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}"
                                        href="{{ route('admin.warehouses.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-building"></i>
                                        </i>
                                        <span class="item-name">Warehouses</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcanany

                @can('cities.read')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}"
                            href="{{ route('admin.cities.index') }}">
                            <i class="icon">
                                <i class="bi bi-geo-alt"></i>
                            </i>
                            <span class="item-name">Cities</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcan

                <!-- Manage Customers -->
                @can('manageCustomers.read')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                            href="{{ route('admin.customers.index') }}">
                            <i class="icon">
                                <i class="bi bi-people"></i>
                            </i>
                            <span class="item-name">Customers</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcan

                <!-- Pages -->
                @canany(['slider.read', 'card.read', 'quick_stats.read', 'why_choose_us.read', 'about_us.read',
                    'showrooms.read', 'privacy_policies.read', 'tnc.read', 'shipping_return.read', 'faqs.read'])
                    <li class="nav-item">
                        <a class="nav-link {{ $isPagesSectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-pages" role="button"
                            aria-expanded="{{ $isPagesSectionActive ? 'true' : 'false' }}" aria-controls="sidebar-pages">
                            <i class="icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </i>
                            <span class="item-name">Pages</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-right"></i>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $isPagesSectionActive ? 'show' : '' }}" id="sidebar-pages"
                            data-bs-parent="#sidebar-menu">

                            <!-- Home Page -->
                            @canany(['slider.read', 'card.read', 'quick_stats.read', 'why_choose_us.read'])
                                <li class="nav-item">
                                    <a class="nav-link {{ $isHomeMgmtActive ? 'section-active' : 'collapsed' }}"
                                        data-bs-toggle="collapse" href="#sidebar-homepage" role="button"
                                        aria-expanded="{{ $isHomeMgmtActive ? 'true' : 'false' }}"
                                        aria-controls="sidebar-homepage">
                                        <i class="icon">
                                            <i class="bi bi-house-door"></i>
                                        </i>
                                        <span class="item-name">Home Page</span>
                                        <i class="right-icon">
                                            <i class="bi bi-chevron-right"></i>
                                        </i>
                                    </a>
                                    <ul class="sub-nav collapse {{ $isHomeMgmtActive ? 'show' : '' }}" id="sidebar-homepage"
                                        data-bs-parent="#sidebar-pages">
                                        @can('slider.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.hero-slider.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.hero-slider.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-images"></i>
                                                    </i>
                                                    <span class="item-name">Hero Slider</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('quick_stats.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.quick-stats.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.quick-stats.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-bar-chart-line"></i>
                                                    </i>
                                                    <span class="item-name">Quick Stats</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('card.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.cards.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-card-list"></i>
                                                    </i>
                                                    <span class="item-name">Feature Cards</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('why_choose_us.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.whyshopwithus.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.whyshopwithus.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-question-circle"></i>
                                                    </i>
                                                    <span class="item-name">Why Shop With Us</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- CMS Pages -->
                            @canany(['about_us.read', 'showrooms.read', 'privacy_policies.read', 'tnc.read',
                                'shipping_return.read'])
                                <li class="nav-item">
                                    <a class="nav-link {{ $isCMSActive ? 'section-active' : 'collapsed' }}"
                                        data-bs-toggle="collapse" href="#sidebar-cms" role="button"
                                        aria-expanded="{{ $isCMSActive ? 'true' : 'false' }}" aria-controls="sidebar-cms">
                                        <i class="icon">
                                            <i class="bi bi-file-text"></i>
                                        </i>
                                        <span class="item-name">CMS Pages</span>
                                        <i class="right-icon">
                                            <i class="bi bi-chevron-right"></i>
                                        </i>
                                    </a>
                                    <ul class="sub-nav collapse {{ $isCMSActive ? 'show' : '' }}" id="sidebar-cms"
                                        data-bs-parent="#sidebar-pages">
                                        @can('about_us.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.about_us.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.about_us.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-info-circle"></i>
                                                    </i>
                                                    <span class="item-name">About Us</span>
                                                </a>
                                            </li>
                                        @endcan

                                        {{-- @can('showrooms.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.showrooms.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.showrooms.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-shop-window"></i>
                                                    </i>
                                                    <span class="item-name">Showrooms</span>
                                                </a>
                                            </li>
                                        @endcan --}}

                                        @can('privacy_policies.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.privacy-policy.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.privacy-policy.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-shield-lock"></i>
                                                    </i>
                                                    <span class="item-name">Privacy Policy</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('tnc.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.tncs.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.tncs.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-file-earmark-ruled"></i>
                                                    </i>
                                                    <span class="item-name">Terms & Conditions</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('shipping_return.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.shipping-return.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.shipping-return.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-truck"></i>
                                                    </i>
                                                    <span class="item-name">Shipping & Returns</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- FAQ Management -->
                            @canany(['faqs.read'])
                                <li class="nav-item">
                                    <a class="nav-link {{ $isFAQActive ? 'section-active' : 'collapsed' }}"
                                        data-bs-toggle="collapse" href="#sidebar-faq" role="button"
                                        aria-expanded="{{ $isFAQActive ? 'true' : 'false' }}" aria-controls="sidebar-faq">
                                        <i class="icon">
                                            <i class="bi bi-question-circle-fill"></i>
                                        </i>
                                        <span class="item-name">FAQ Management</span>
                                        <i class="right-icon">
                                            <i class="bi bi-chevron-right"></i>
                                        </i>
                                    </a>
                                    <ul class="sub-nav collapse {{ $isFAQActive ? 'show' : '' }}" id="sidebar-faq"
                                        data-bs-parent="#sidebar-pages">
                                        @can('faqs.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.faq-categories.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-list-ul"></i>
                                                    </i>
                                                    <span class="item-name">FAQ Categories</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('faqs.read')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
                                                    href="{{ route('admin.faqs.index') }}">
                                                    <i class="icon">
                                                        <i class="bi bi-chat-left-text"></i>
                                                    </i>
                                                    <span class="item-name">FAQs</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcanany

                <!-- Orders and Dispatch -->
                @canany(['order.read', 'dispatch.read'])
                    <li class="nav-item">
                        <a class="nav-link {{ $isOrdersSectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-orders" role="button"
                            aria-expanded="{{ $isOrdersSectionActive ? 'true' : 'false' }}"
                            aria-controls="sidebar-orders">
                            <i class="icon">
                                <i class="bi bi-receipt"></i>
                            </i>
                            <span class="item-name">Orders & Dispatch</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-right"></i>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $isOrdersSectionActive ? 'show' : '' }}" id="sidebar-orders"
                            data-bs-parent="#sidebar-menu">
                            @can('order.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                                        href="{{ route('admin.orders.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-cart-check"></i>
                                        </i>
                                        <span class="item-name">Orders</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="hr-horizontal">
                                </li>
                            @endcan

                            @can('dispatch.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dispatch.*') ? 'active' : '' }}"
                                        href="{{ route('admin.dispatch.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-truck"></i>
                                        </i>
                                        <span class="item-name">Dispatch</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcanany


                <!-- Reviews -->
                @can('reviews.read')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index') }}">
                            <i class="icon">
                                <i class="bi bi-star"></i>
                            </i>
                            <span class="item-name">Reviews</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcan

                <!-- Newsletter -->
                @can('newsletter.read')
                    <li class="nav-item">
                        <a class="nav-link {{ $isNewsletterSectionActive ? 'active' : '' }}"
                            href="{{ route('admin.newsletter-subscribers.index') }}">
                            <i class="icon">
                                <i class="bi bi-envelope"></i>
                            </i>
                            <span class="item-name">Newsletter</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcan

                <!-- Coupons & Discounts -->
                @canany(['coupon.read', 'discount.read'])
                    <li class="nav-item">
                        <a class="nav-link {{ $isCouponSectionActive || $isDiscountSectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-coupons-discounts" role="button"
                            aria-expanded="{{ $isCouponSectionActive || $isDiscountSectionActive ? 'true' : 'false' }}"
                            aria-controls="sidebar-coupons-discounts">
                            <i class="icon">
                                <i class="bi bi-tags"></i>
                            </i>
                            <span class="item-name">Coupons & Discounts</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-down"></i>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $isCouponSectionActive || $isDiscountSectionActive ? 'show' : '' }}"
                            id="sidebar-coupons-discounts" data-bs-parent="#sidebar-menu">
                            @can('coupon.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                                        href="{{ route('admin.coupons.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-ticket-perforated"></i>
                                        </i>
                                        <span class="item-name">Coupons</span>
                                    </a>
                                </li>
                            @endcan
                            @can('coupon.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.coupon-usage.*') ? 'active' : '' }}"
                                        href="{{ route('admin.coupon-usage.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-clipboard-check"></i>
                                        </i>
                                        <span class="item-name">Coupon Usage</span>
                                    </a>
                                </li>
                            @endcan
                            @can('discount.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}"
                                        href="{{ route('admin.discounts.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-percent"></i>
                                        </i>
                                        <span class="item-name">Discounts</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcanany

                <!-- User & Access -->
                @canany(['user management.read', 'role.read'])
                    <li class="nav-item">
                        <a class="nav-link {{ $isUserSectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-users" role="button"
                            aria-expanded="{{ $isUserSectionActive ? 'true' : 'false' }}" aria-controls="sidebar-users">
                            <i class="icon">
                                <i class="bi bi-shield-check"></i>
                            </i>
                            <span class="item-name">User & Access</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-right"></i>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $isUserSectionActive ? 'show' : '' }}" id="sidebar-users"
                            data-bs-parent="#sidebar-menu">
                            @can('user management.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}"
                                        href="{{ route('admin.user-management.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-person"></i>
                                        </i>
                                        <span class="item-name">Users</span>
                                    </a>
                                </li>
                            @endcan

                            @can('role.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.roles-permissions.*') ? 'active' : '' }}"
                                        href="{{ route('admin.roles-permissions.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-key"></i>
                                        </i>
                                        <span class="item-name">Roles & Permissions</span>
                                    </a>
                                </li>
                            @endcan
                            @can('site_status.read')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}"
                                        href="{{ route('admin.site-settings.index') }}">
                                        <i class="icon">
                                            <i class="bi bi-gear"></i>
                                        </i>
                                        <span class="item-name">Site Settings</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcanany

                <!-- Contact Us -->
                @can('contact_us.read')
                    <li class="nav-item">
                        <a class="nav-link {{ $isContactUsSectionActive ? 'active' : '' }}"
                            href="{{ route('admin.contact-us.index') }}">
                            <i class="icon">
                                <i class="bi bi-envelope-at"></i>
                            </i>
                            <span class="item-name">Contact Us</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcan

                @can('napsLogs.read')
                <li class="nav-item">
                    <a class="nav-link {{ $isContactUsSectionActive ? 'active' : '' }}"
                        href="{{ route('admin.naps-log.index') }}">
                        <i class="icon">
                            <i class="bi bi-activity"></i>
                        </i>
                        <span class="item-name">NAPS Payment Logs</span>
                    </a>
                </li>
                <li>
                    <hr class="hr-horizontal">
                </li>
                @endcan

                <!-- Reports -->
                @can('reports.read')
                    <li class="nav-item">
                        <a class="nav-link {{ $isReportsSectionActive ? 'section-active' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#sidebar-reports" role="button"
                            aria-expanded="{{ $isReportsSectionActive ? 'true' : 'false' }}"
                            aria-controls="sidebar-reports">
                            <i class="icon">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </i>
                            <span class="item-name">Reports</span>
                            <i class="right-icon">
                                <i class="bi bi-chevron-right"></i>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $isReportsSectionActive ? 'show' : '' }}" id="sidebar-reports"
                            data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                                    href="{{ route('admin.reports.stock') }}">
                                    <i class="icon">
                                        <i class="bi bi-file-earmark-bar-graph"></i>
                                    </i>
                                    <span class="item-name">Stock Report</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</aside>
