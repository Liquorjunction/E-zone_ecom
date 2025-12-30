<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all" style="height:100vh;display:flex;flex-direction:column;">
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

    <div class="sidebar-body pt-0" data-scrollbar style="overflow:auto;flex:1 1 auto;">
        <div class="sidebar-list">
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#sidebar-home" role="button"
                        aria-expanded="{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }}"
                        aria-controls="sidebar-home">
                        <i class="icon" style="display: inline-flex;">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="icon-20">
                                <path opacity="0.4"
                                    d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                    fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                    fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">Home</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ request()->routeIs('admin.dashboard') ? 'show' : '' }}"
                        id="sidebar-home" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                <i class="icon" style="display: inline-flex;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
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
                    $isDiscountSectionActive = request()->routeIs('admin.discounts.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isDiscountSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-discounts" role="button"
                        aria-expanded="{{ $isDiscountSectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-discounts">
                        <i class="icon" style="display: inline-flex;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M7 12l5 5 5-10" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                        </i>
                        <span class="item-name">Discounts & Promotions</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isDiscountSectionActive ? 'show' : '' }}" id="sidebar-discounts" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.discounts.index') ? 'active' : '' }}" href="{{ route('admin.discounts.index') }}">
                                <i class="icon" style="display: inline-flex;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </i>
                                <span class="item-name">All Discounts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.discounts.create') ? 'active' : '' }}" href="{{ route('admin.discounts.create') }}">
                                <i class="icon" style="display: inline-flex;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </i>
                                <span class="item-name">Create Discount</span>
                            </a>
                        </li>
                    </ul>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </i>
                        <span class="item-name">Product Management</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isProductSectionActive ? 'show' : '' }}" id="sidebar-products"
                        data-bs-parent="#sidebar-menu">
                        @can('brands.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                                    href="{{ route('admin.brands.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                        </svg>
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
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.24 2H5.34C3.15 2 2 3.15 2 5.33V7.23C2 9.41 3.15 10.56 5.33 10.56H7.23C9.41 10.56 10.56 9.41 10.56 7.23V5.33C10.57 3.15 9.42 2 7.24 2Z"
                                                fill="currentColor" />
                                            <path
                                                d="M18.6695 2H16.7695C14.5895 2 13.4395 3.15 13.4395 5.33V7.23C13.4395 9.41 14.5895 10.56 16.7695 10.56H18.6695C20.8495 10.56 21.9995 9.41 21.9995 7.23V5.33C21.9995 3.15 20.8495 2 18.6695 2Z"
                                                fill="currentColor" />
                                            <path
                                                d="M18.6695 13.4297H16.7695C14.5895 13.4297 13.4395 14.5797 13.4395 16.7597V18.6597C13.4395 20.8397 14.5895 21.9897 16.7695 21.9897H18.6695C20.8495 21.9897 21.9995 20.8397 21.9995 18.6597V16.7597C21.9995 14.5797 20.8495 13.4297 18.6695 13.4297Z"
                                                fill="currentColor" />
                                            <path
                                                d="M7.24 13.4297H5.34C3.15 13.4297 2 14.5797 2 16.7597V18.6597C2 20.8497 3.15 21.9997 5.33 21.9997H7.23C9.41 21.9997 10.56 20.8497 10.56 18.6697V16.7697C10.57 14.5797 9.42 13.4297 7.24 13.4297Z"
                                                fill="currentColor" />
                                        </svg>
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
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 6H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path d="M3 12H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path d="M3 18H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>
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
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M20 7H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2z">
                                            </path>
                                            <path d="M9 22V7"></path>
                                            <path d="M15 22V7"></path>
                                            <path d="M2 7h20"></path>
                                        </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3h7v7H3z"></path>
                                <path d="M14 3h7v7h-7z"></path>
                                <path d="M14 14h7v7h-7z"></path>
                                <path d="M3 14h7v7H3z"></path>
                            </svg>
                        </i>
                        <span class="item-name">Inventory</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isInventorySectionActive ? 'show' : '' }}" id="sidebar-inventory"
                        data-bs-parent="#sidebar-menu">
                        @can('inventories.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                                    href="{{ route('admin.inventory.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="3" width="18" height="18" rx="2" />
                                            <path d="M3 9h18M3 15h18M9 3v18M15 3v18" />
                                        </svg>
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
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                        </svg>
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
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                        </svg>
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
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 3h18v18H3z"></path>
                                            <path d="M3 9h18"></path>
                                            <path d="M3 15h18"></path>
                                            <path d="M9 3v18"></path>
                                            <path d="M15 3v18"></path>
                                        </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </i>
                        <span class="item-name">Pages</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
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
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2 9C2 7.89543 2.89543 7 4 7H20C21.1046 7 22 7.89543 22 9V15C22 16.1046 21.1046 17 20 17H4C2.89543 17 2 16.1046 2 15V9Z"
                                                        stroke="currentColor" stroke-width="1.5" />
                                                    <path d="M22 9L20 7M2 9L4 7M22 15L20 17M2 15L4 17"
                                                        stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </i>
                                            <span class="item-name">Hero Slider</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('card.read')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}"
                                            href="{{ route('admin.cards.index') }}">
                                            <i class="icon" style="display: inline-flex;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2 12.6099H19" stroke="currentColor" stroke-width="1.5"
                                                        stroke-miterlimit="10" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M19 10.2798V17.4298C18.97 20.2798 18.19 20.9998 15.22 20.9998H5.78003C2.76003 20.9998 2 20.2498 2 17.2698V10.2798C2 7.5798 2.63 6.70981 5 6.56981C5.24 6.55981 5.50003 6.5498 5.78003 6.5498H15.22C18.24 6.5498 19 7.2998 19 10.2798Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path opacity="0.4"
                                                        d="M22 6.73V13.72C22 16.42 21.37 17.29 19 17.43V10.28C19 7.3 18.24 6.55 15.22 6.55H5.78003C5.50003 6.55 5.24 6.56 5 6.57C5.03 3.72 5.81003 3 8.78003 3H18.22C21.24 3 22 3.75 22 6.73Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path opacity="0.4" d="M5.25 17.8101H6.96997" stroke="currentColor"
                                                        stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path opacity="0.4" d="M9.10938 17.8101H12.5494"
                                                        stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="8" r="4"></circle>
                                                    <path d="M4 20c1.5-4 14.5-4 16 0"></path>
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 3l8 4v5c0 5-3.5 9-8 9s-8-4-8-9V7l8-4z"></path>
                                                    <path d="M9 12l2 2 4-4"></path>
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                    </path>
                                                    <path d="M14 2v6h6"></path>
                                                    <line x1="8" y1="13" x2="16" y2="13">
                                                    </line>
                                                    <line x1="8" y1="17" x2="14" y2="17">
                                                    </line>
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1" y="3" width="15" height="13"></rect>
                                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                                    <circle cx="5.5" cy="18.5" r="1.5"></circle>
                                                    <circle cx="18.5" cy="18.5" r="1.5"></circle>
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3 7h5l2 2h11v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"></path>
                                                </svg>
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
                                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path d="M9 9a3 3 0 116 0c0 2-3 2-3 4"></path>
                                                    <circle cx="12" cy="17" r="1"></circle>
                                                </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </i>
                        <span class="item-name">User & Access</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isUserSectionActive ? 'show' : '' }}" id="sidebar-users"
                        data-bs-parent="#sidebar-menu">
                        @can('user management.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}"
                                    href="{{ route('admin.user-management.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M16.1583 8.23285C16.1583 10.5825 14.2851 12.4666 11.949 12.4666C9.61292 12.4666 7.73974 10.5825 7.73974 8.23285C7.73974 5.88227 9.61292 4 11.949 4C14.2851 4 16.1583 5.88227 16.1583 8.23285ZM11.949 20C8.51785 20 5.58809 19.456 5.58809 17.2802C5.58809 15.1034 8.49904 14.5396 11.949 14.5396C15.3802 14.5396 18.31 15.0836 18.31 17.2604C18.31 19.4362 15.399 20 11.949 20ZM17.9571 8.30922C17.9571 9.50703 17.5998 10.6229 16.973 11.5505C16.9086 11.646 16.9659 11.7748 17.0796 11.7946C17.2363 11.8216 17.3984 11.8369 17.5631 11.8414C19.2062 11.8846 20.6809 10.821 21.0883 9.21974C21.6918 6.84123 19.9198 4.7059 17.6634 4.7059C17.4181 4.7059 17.1835 4.73201 16.9551 4.77884C16.9238 4.78605 16.8907 4.80046 16.8728 4.82838C16.8513 4.8626 16.8674 4.90853 16.8889 4.93825C17.5667 5.8938 17.9571 7.05918 17.9571 8.30922ZM20.6782 13.5126C21.7823 13.7296 22.5084 14.1727 22.8093 14.8166C23.0636 15.3453 23.0636 15.9586 22.8093 16.4864C22.349 17.4851 20.8654 17.8058 20.2887 17.8886C20.1696 17.9066 20.0738 17.8031 20.0864 17.6833C20.3809 14.9157 18.0377 13.6035 17.4315 13.3018C17.4055 13.2883 17.4002 13.2676 17.4028 13.255C17.4046 13.246 17.4154 13.2316 17.4351 13.2289C18.7468 13.2046 20.1571 13.3847 20.6782 13.5126ZM6.43711 11.8413C6.60186 11.8368 6.76304 11.8224 6.92063 11.7945C7.03434 11.7747 7.09165 11.6459 7.02718 11.5504C6.4004 10.6228 6.04313 9.50694 6.04313 8.30913C6.04313 7.05909 6.43353 5.89371 7.11135 4.93816C7.13284 4.90844 7.14806 4.86251 7.12746 4.82829C7.10956 4.80127 7.07553 4.78596 7.04509 4.77875C6.81586 4.73192 6.58127 4.70581 6.33593 4.70581C4.07951 4.70581 2.30751 6.84114 2.91191 9.21965C3.31932 10.8209 4.79405 11.8845 6.43711 11.8413ZM6.59694 13.2545C6.59962 13.268 6.59425 13.2878 6.56918 13.3022C5.9621 13.6039 3.61883 14.9161 3.91342 17.6827C3.92595 17.8034 3.83104 17.9061 3.71195 17.889C3.13531 17.8061 1.65163 17.4855 1.19139 16.4867C0.936203 15.9581 0.936203 15.3457 1.19139 14.817C1.49225 14.1731 2.21752 13.73 3.32156 13.512C3.84358 13.385 5.25294 13.2049 6.5656 13.2292C6.5853 13.2319 6.59515 13.2464 6.59694 13.2545Z"
                                                fill="currentColor"></path>
                                        </svg>
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
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M17.5227 7.39601V8.92935C19.2451 9.46696 20.5 11.0261 20.5 12.8884V17.8253C20.5 20.1308 18.5886 22 16.2322 22H7.7688C5.41136 22 3.5 20.1308 3.5 17.8253V12.8884C3.5 11.0261 4.75595 9.46696 6.47729 8.92935V7.39601C6.48745 4.41479 8.95667 2 11.9848 2C15.0535 2 17.5227 4.41479 17.5227 7.39601ZM12.0051 3.73904C14.0678 3.73904 15.7445 7.39601V8.7137H8.25553V7.37613C8.26569 5.36878 9.94232 3.73904 12.0051 3.73904ZM12.8891 16.4549C12.8891 16.9419 12.4928 17.3294 11.9949 17.3294C11.5072 17.3294 11.1109 16.9419 11.1109 16.4549V14.2488C11.1109 13.7718 11.5072 13.3843 11.9949 13.3843C12.4928 13.3843 12.8891 13.7718 12.8891 14.2488V16.4549Z"
                                                fill="currentColor"></path>
                                        </svg>
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
                    $isOrderSectionActive =
                        request()->routeIs('admin.orders.*') ||
                        request()->routeIs('admin.return-requests.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isOrderSectionActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#sidebar-orders" role="button"
                        aria-expanded="{{ $isOrderSectionActive ? 'true' : 'false' }}"
                        aria-controls="sidebar-orders">
                        <i class="icon" style="display: inline-flex;">
                            <svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m5-8v8m4-8v8m1-13h2"></path>
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                            </svg>
                        </i>
                        <span class="item-name">Orders & Returns</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse {{ $isOrderSectionActive ? 'show' : '' }}" id="sidebar-orders"
                        data-bs-parent="#sidebar-menu">
                        @can('order.read')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                                    href="{{ route('admin.orders.index') }}">
                                    <i class="icon" style="display: inline-flex;">
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.5 4C2.67157 4 2 4.67157 2 5.5V18.5C2 19.3284 2.67157 20 3.5 20H20.5C21.3284 20 22 19.3284 22 18.5V5.5C22 4.67157 21.3284 4 20.5 4H3.5ZM3.5 6C3.22386 6 3 6.22386 3 6.5V18.5C3 18.7761 3.22386 19 3.5 19H20.5C20.7761 19 21 18.7761 21 18.5V6.5C21 6.22386 20.7761 6 20.5 6H3.5Z"
                                                fill="currentColor"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7 9C6.44772 9 6 9.44772 6 10C6 10.5523 6.44772 11 7 11H17C17.5523 11 18 10.5523 18 10C18 9.44772 17.5523 9 17 9H7ZM6 13C6 12.4477 6.44772 12 7 12H13C13.5523 12 14 12.4477 14 13C14 13.5523 13.5523 14 13 14H7C6.44772 14 6 13.5523 6 13Z"
                                                fill="currentColor"></path>
                                        </svg>
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
