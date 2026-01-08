<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all"
    style="height:100vh;display:flex;flex-direction:column;">
    <div class="sidebar-header d-flex align-items-center justify-content-center px-3 py-3">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo" class="img-fluid"
                        style="height: 120px; width: 120px; object-fit: contain;" />
                </div>
                <div class="logo-mini">
                    <img src="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" alt="E-Zone Mini Logo"
                        class="img-fluid" style="height: 50px; width: 50px; object-fit: contain;" />
                </div>
            </div>
        </a>

        <!-- Pin/Unpin Button -->
        <button class="sidebar-pin-btn" id="sidebarPinBtn" title="Pin Sidebar" aria-label="Pin Sidebar">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 12V4H8V12H6V14H11V21H13V14H18V12H16Z" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </svg>
        </button>

        <button class="sidebar-toggle btn btn-sm btn-link p-0" data-toggle="sidebar" data-active="true"
            title="Toggle Sidebar" aria-label="Toggle Sidebar" style="display: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    <div class="sidebar-body pt-0" data-scrollbar style="overflow:auto;flex:1 1 auto; padding-right:0 !important;">
        <div class="sidebar-list">
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#sidebar-home" role="button"
                        aria-expanded="{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }}"
                        aria-controls="sidebar-home">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-house" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">Home</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ request()->routeIs('admin.dashboard') ? 'show' : '' }}"
                        id="sidebar-home" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                <i class="icon" style="display: inline-flex;">
                                    <i class="bi bi-grid" style="font-size:20px;"></i>
                                </i>
                                <span class="item-name">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </li>


                <li>
                    <hr class="hr-horizontal">
                </li>

                @php
                    $isProductSectionActive =
                        request()->routeIs('admin.brands.*') ||
                        request()->routeIs('admin.category-subcategory.*') ||
                        request()->routeIs('admin.products.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isProductSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-products" role="button"
                        aria-expanded="{{ $isProductSectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-products">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-box-seam" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">Product Management</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isProductSectionActive ? 'show' : '' }}" id="sidebar-products"
                        data-bs-parent="#sidebar-menu">
                        @can('brands.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                                    href="{{ route('admin.brands.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-tags" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Brands</span>
                                </a>
                            </li>
                        @endcan

                        @can('category.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.category-subcategory.categories.*') ? 'active' : '' }}"
                                    href="{{ route('admin.category-subcategory.categories.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-list" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Categories</span>
                                </a>
                            </li>
                        @endcan

                        @can('subcategory.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.category-subcategory.subcategories.*') ? 'active' : '' }}"
                                    href="{{ route('admin.category-subcategory.subcategories.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-list-check" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Subcategories</span>
                                </a>
                            </li>
                        @endcan

                        @can('products.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                                    href="{{ route('admin.products.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-box" style="font-size:20px;"></i>
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

                @php
                    $isInventorySectionActive =
                        request()->routeIs('admin.stores.*') || request()->routeIs('admin.warehouses.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isInventorySectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-inventory" role="button"
                        aria-expanded="{{ $isInventorySectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-inventory">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-boxes" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">Inventory</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isInventorySectionActive ? 'show' : '' }}" id="sidebar-inventory"
                        data-bs-parent="#sidebar-menu">
                        @can('inventories.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                                    href="{{ route('admin.inventory.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-archive" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Inventory</span>
                                </a>
                            </li>
                        @endcan

                        @can('inventory-transaction.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.inventory-transactions.*') ? 'active' : '' }}"
                                    href="{{ route('admin.inventory-transactions.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-arrow-repeat" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Inventory Transactions</span>
                                </a>
                            </li>
                        @endcan

                        @can('store.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}"
                                    href="{{ route('admin.stores.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-shop" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Stores</span>
                                </a>
                            </li>
                        @endcan

                        @can('warehouse.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}"
                                    href="{{ route('admin.warehouses.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-building" style="font-size:20px;"></i>
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

                @php
                    // Check if any sub-route inside the main 'Pages' wrapper is active
                    $isPagesSectionActive =
                        request()->routeIs('admin.hero-slider.*') ||
                        request()->routeIs('admin.cards.*') ||
                        request()->routeIs('admin.about_us.*') ||
                        request()->routeIs('admin.showrooms.*') ||
                        request()->routeIs('admin.privacy-policy.*') ||
                        request()->routeIs('admin.tncs.*') ||
                        request()->routeIs('admin.shipping-return.*') ||
                        request()->routeIs('admin.faq-categories.*') ||
                        request()->routeIs('admin.faqs.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isPagesSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-pages-main" role="button"
                        aria-expanded="{{ $isPagesSectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-pages-main">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-file-earmark-text" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">Pages</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>

                    <ul class="sub-nav collapse {{ $isPagesSectionActive ? 'show' : '' }}" id="sidebar-pages-main"
                        data-bs-parent="#sidebar-menu">

                        @php
                            $isHomeMgmtActive =
                                request()->routeIs('admin.hero-slider.*') || request()->routeIs('admin.cards.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isHomeMgmtActive ? '' : 'collapsed' }}" href="#"
                                data-bs-toggle="collapse" data-bs-target="#sidebar-homepage"
                                aria-expanded="{{ $isHomeMgmtActive ? 'true' : 'false' }}"
                                aria-controls="sidebar-homepage">
                                <i class="icon" style="display: inline-flex;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3 9.85714L12 4L21 9.85714V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V9.85714Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M3 9.85714H21" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </i>
                                <span class="item-name">Home Page</span>
                                <i class="right-icon">
                                    <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </i>
                            </a>
                            <ul class="sub-nav collapse {{ $isHomeMgmtActive ? 'show' : '' }}" id="sidebar-homepage"
                                data-bs-parent="#sidebar-pages-main">
                                @can('slider.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.hero-slider.*') ? 'active' : '' }}"
                                            href="{{ route('admin.hero-slider.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-images" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Hero Slider</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('quick_stats.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.quick-stats.*') ? 'active' : '' }}"
                                            href="{{ route('admin.quick-stats.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-bar-chart-line" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Quick Stats</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('card.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}"
                                            href="{{ route('admin.cards.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-card-list" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Feature Cards</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                        @php
                            $isCMSActive =
                                request()->routeIs('admin.about_us.*') ||
                                request()->routeIs('admin.showrooms.*') ||
                                request()->routeIs('admin.privacy-policy.*') ||
                                request()->routeIs('admin.tncs.*') ||
                                request()->routeIs('admin.shipping-return.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isCMSActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                                href="#sidebar-cms-pages" role="button"
                                aria-expanded="{{ $isCMSActive ? 'true' : 'false' }}"
                                aria-controls="sidebar-cms-pages">
                                <i class="icon" style="display: inline-flex;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </i>
                                <span class="item-name">CMS Pages</span>
                                <i class="right-icon">
                                    <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </i>
                            </a>

                            <ul class="sub-nav collapse {{ $isCMSActive ? 'show' : '' }}" id="sidebar-cms-pages"
                                data-bs-parent="#sidebar-pages-main">
                                @can('about_us.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.about_us.*') ? 'active' : '' }}"
                                            href="{{ route('admin.about_us.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-info-circle" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">About Us</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('showrooms.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.showrooms.*') ? 'active' : '' }}"
                                            href="{{ route('admin.showrooms.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-shop-window" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Showrooms</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('privacy_policies.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.privacy-policy.*') ? 'active' : '' }}"
                                            href="{{ route('admin.privacy-policy.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-shield-lock" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Privacy Policy</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('tnc.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.tncs.*') ? 'active' : '' }}"
                                            href="{{ route('admin.tncs.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-file-earmark-text" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Terms & Conditions</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('shipping_return.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.shipping-return.*') ? 'active' : '' }}"
                                            href="{{ route('admin.shipping-return.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-truck" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">Shipping & Returns</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                        @php
                            $isFAQActive =
                                request()->routeIs('admin.faq-categories.*') || request()->routeIs('admin.faqs.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isFAQActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                                href="#sidebar-faqs-wrap" role="button"
                                aria-expanded="{{ $isFAQActive ? 'true' : 'false' }}"
                                aria-controls="sidebar-faqs-wrap">
                                <i class="icon" style="display: inline-flex;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </i>
                                <span class="item-name">FAQ Management</span>
                                <i class="right-icon">
                                    <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </i>
                            </a>
                            <ul class="sub-nav collapse {{ $isFAQActive ? 'show' : '' }}" id="sidebar-faqs-wrap"
                                data-bs-parent="#sidebar-pages-main">
                                @can('faqs.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"
                                            href="{{ route('admin.faq-categories.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-list" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">FAQ Categories</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('faqs.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
                                            href="{{ route('admin.faqs.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <i class="bi bi-question-circle" style="font-size:20px;"></i>
                                            </i>
                                            <span class="item-name">FAQs</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                </li>

                <li>
                    <hr class="hr-horizontal">
                </li>

                @php
                    $isUserSectionActive =
                        request()->routeIs('admin.user-management.*') ||
                        request()->routeIs('admin.roles-permissions.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isUserSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-users" role="button"
                        aria-expanded="{{ $isUserSectionActive ? 'true' : 'false' }}" aria-controls="sidebar-users">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-people" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">User & Access</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isUserSectionActive ? 'show' : '' }}" id="sidebar-users"
                        data-bs-parent="#sidebar-menu">
                        @can('user management.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}"
                                    href="{{ route('admin.user-management.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-person" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Users</span>
                                </a>
                            </li>
                        @endcan

                        @can('role.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.roles-permissions.*') ? 'active' : '' }}"
                                    href="{{ route('admin.roles-permissions.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-shield-lock" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Roles & Permissions</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>

                <li>
                    <hr class="hr-horizontal">
                </li>

                @php
                    $isDiscountSectionActive = request()->routeIs('admin.discounts.*') || request()->routeIs('admin.coupons.*') || request()->routeIs('admin.coupon-usage.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isDiscountSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-discounts" role="button"
                        aria-expanded="{{ $isDiscountSectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-discounts">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-percent" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">Discounts & Promotions</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isDiscountSectionActive ? 'show' : '' }}" id="sidebar-discounts"
                        data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.discounts.index') ? 'active' : '' }}"
                                href="{{ route('admin.discounts.index') }}">
                                <i class="icon" style="display: inline-flex;">
                                    <i class="bi bi-tag" style="font-size:20px;"></i>
                                </i>
                                <span class="item-name">Discounts</span>
                            </a>
                        </li>
                        @can('coupon.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                                    href="{{ route('admin.coupons.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-ticket-perforated" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Coupons</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.coupon-usage.*') ? 'active' : '' }}"
                                    href="{{ route('admin.coupon-usage.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-graph-up" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Coupon Usage</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>

                <li>
                    <hr class="hr-horizontal">
                </li>

                @php
                    $isOrderSectionActive =
                        request()->routeIs('admin.orders.*') || request()->routeIs('admin.return-requests.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isOrderSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-orders" role="button"
                        aria-expanded="{{ $isOrderSectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-orders">
                        <i class="icon" style="display: inline-flex;">
                            <i class="bi bi-cart-check" style="font-size:20px;"></i>
                        </i>
                        <span class="item-name">Orders & Returns</span>
                        <i class="right-icon">
                            <i class="bi bi-chevron-right" style="font-size:16px;"></i>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isOrderSectionActive ? 'show' : '' }}" id="sidebar-orders"
                        data-bs-parent="#sidebar-menu">
                        @can('order.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                                    href="{{ route('admin.orders.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <i class="bi bi-receipt" style="font-size:20px;"></i>
                                    </i>
                                    <span class="item-name">Orders</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-footer"></div>
</aside>
