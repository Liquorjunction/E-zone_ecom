<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand">

            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="logo" class="img-fluid">
                </div>
                <div class="logo-mini">
                    <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2"
                            transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                        <rect x="7.72803" y="27.728" width="28" height="4" rx="2"
                            transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                        <rect x="10.5366" y="16.3945" width="16" height="4" rx="2"
                            transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                        <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2"
                            transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                    </svg>
                </div>
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>
    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            {{-- TODO: Consider moving this to a partial view like @include('layouts.partials.sidebar') --}}
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Use route() helper for dynamic links. Example: route('dashboard') --}}
                    <a class="nav-link active" aria-current="page" href="{{ route('admin.dashboard') }}">
                        <i class="icon">
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
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>
                <li>
                    <hr class="hr-horizontal">
                </li>
                @can('user management.read')
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Management</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>
                    @can('user management.read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.user-management.index') }}">
                                <i class="icon">
                                    <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" -
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" -
                                            d="M16.1583 8.23285C16.1583 10.5825 14.2851 12.4666 11.949 12.4666C9.61292 12.4666 7.73974 10.5825 7.73974 8.23285C7.73974 5.88227 9.61292 4 11.949 4C14.2851 4 16.1583 5.88227 16.1583 8.23285ZM11.949 20C8.51785 20 5.58809 19.456 5.58809 17.2802C5.58809 15.1034 8.49904 14.5396 11.949 14.5396C15.3802 14.5396 18.31 15.0836 18.31 17.2604C18.31 19.4362 15.399 20 11.949 20ZM17.9571 8.30922C17.9571 9.50703 17.5998 10.6229 16.973 11.5505C16.9086 11.646 16.9659 11.7748 17.0796 11.7946C17.2363 11.8216 17.3984 11.8369 17.5631 11.8414C19.2062 11.8846 20.6809 10.821 21.0883 9.21974C21.6918 6.84123 19.9198 4.7059 17.6634 4.7059C17.4181 4.7059 17.1835 4.73201 16.9551 4.77884C16.9238 4.78605 16.8907 4.80046 16.8728 4.82838C16.8513 4.8626 16.8674 4.90853 16.8889 4.93825C17.5667 5.8938 17.9571 7.05918 17.9571 8.30922ZM20.6782 13.5126C21.7823 13.7296 22.5084 14.1727 22.8093 14.8166C23.0636 15.3453 23.0636 15.9586 22.8093 16.4864C22.349 17.4851 20.8654 17.8058 20.2887 17.8886C20.1696 17.9066 20.0738 17.8031 20.0864 17.6833C20.3809 14.9157 18.0377 13.6035 17.4315 13.3018C17.4055 13.2883 17.4002 13.2676 17.4028 13.255C17.4046 13.246 17.4154 13.2316 17.4351 13.2289C18.7468 13.2046 20.1571 13.3847 20.6782 13.5126ZM6.43711 11.8413C6.60186 11.8368 6.76304 11.8224 6.92063 11.7945C7.03434 11.7747 7.09165 11.6459 7.02718 11.5504C6.4004 10.6228 6.04313 9.50694 6.04313 8.30913C6.04313 7.05909 6.43353 5.89371 7.11135 4.93816C7.13284 4.90844 7.14806 4.86251 7.12746 4.82829C7.10956 4.80127 7.07553 4.78596 7.04509 4.77875C6.81586 4.73192 6.58127 4.70581 6.33593 4.70581C4.07951 4.70581 2.30751 6.84114 2.91191 9.21965C3.31932 10.8209 4.79405 11.8845 6.43711 11.8413ZM6.59694 13.2545C6.59962 13.268 6.59425 13.2878 6.56918 13.3022C5.9621 13.6039 3.61883 14.9161 3.91342 17.6827C3.92595 17.8034 3.83104 17.9061 3.71195 17.889C3.13531 17.8061 1.65163 17.4855 1.19139 16.4867C0.936203 15.9581 0.936203 15.3457 1.19139 14.817C1.49225 14.1731 2.21752 13.73 3.32156 13.512C3.84358 13.385 5.25294 13.2049 6.5656 13.2292C6.5853 13.2319 6.59515 13.2464 6.59694 13.2545Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </i>
                                <span class="item-name">User Management</span>
                            </a>
                        </li>
                    @endcan
                    @can('role.read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.roles-permissions.index') }}">
                                <i class="icon">
                                    <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" -
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" -
                                            d="M17.5227 7.39601V8.92935C19.2451 9.46696 20.5 11.0261 20.5 12.8884V17.8253C20.5 20.1308 18.5886 22 16.2322 22H7.7688C5.41136 22 3.5 20.1308 3.5 17.8253V12.8884C3.5 11.0261 4.75595 9.46696 6.47729 8.92935V7.39601C6.48745 4.41479 8.95667 2 11.9848 2C15.0535 2 17.5227 4.41479 17.5227 7.39601ZM12.0051 3.73904C14.0678 3.73904 15.7445 7.39601V8.7137H8.25553V7.37613C8.26569 5.36878 9.94232 3.73904 12.0051 3.73904ZM12.8891 16.4549C12.8891 16.9419 12.4928 17.3294 11.9949 17.3294C11.5072 17.3294 11.1109 16.9419 11.1109 16.4549V14.2488C11.1109 13.7718 11.5072 13.3843 11.9949 13.3843C12.4928 13.3843 12.8891 13.7718 12.8891 14.2488V16.4549Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </i>
                                <span class="item-name">Role & Permission</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                @endcan
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Master's</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                @can('slider.read')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.hero-slider.index') }}">
                            <i class="icon">
                                <svg fill="#000000" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                    viewBox="0 0 72 72" enable-background="new 0 0 72 72" xml:space="preserve">
                                    <g>
                                        <g>
                                            <path d="M68.5,61.568c0,3.828-3.104,6.932-6.932,6.932H10.432c-3.828,0-6.932-3.104-6.932-6.932V26.432
                                                                c0-3.828,3.104-6.932,6.932-6.932h51.137c3.828,0,6.932,3.104,6.932,6.932V61.568z M64.5,26.432c0-1.619-1.313-2.932-2.932-2.932
                                                                H10.432c-1.619,0-2.932,1.313-2.932,2.932v35.137c0,1.619,1.313,2.932,2.932,2.932h51.137c1.619,0,2.932-1.313,2.932-2.932V26.432
                                                        z" />
                                        </g>
                                        <g>
                                            <g>
                                                <path
                                                    d="M12.5,37.5c-0.553,0-1-0.447-1-1v-3.802c0-2.721,2.313-5.198,4.903-5.198h18.051c0.552,0,1,0.447,1,1s-0.448,1-1,1
                                                                    H16.403c-1.461,0-2.903,1.614-2.903,3.198V36.5C13.5,37.053,13.052,37.5,12.5,37.5z" />
                                            </g>
                                            <g>
                                                <path
                                                    d="M12.5,42.5c-0.553,0-1-0.447-1-1v-2c0-0.553,0.447-1,1-1c0.552,0,1,0.447,1,1v2C13.5,42.053,13.052,42.5,12.5,42.5z" />
                                            </g>
                                        </g>
                                        <g>
                                            <path
                                                d="M60.454,15.5h-49c-1.104,0-2-0.896-2-2s0.896-2,2-2h49c1.104,0,2,0.896,2,2S61.559,15.5,60.454,15.5z" />
                                        </g>
                                        <g>
                                            <path
                                                d="M54.454,7.5h-37c-1.104,0-2-0.896-2-2s0.896-2,2-2h37c1.104,0,2,0.896,2,2S55.559,7.5,54.454,7.5z" />
                                        </g>
                                    </g>
                                </svg>
                            </i>
                            <span class="item-name">Slider</span>
                        </a>
                    </li>
                @endcan
                @can('category.read')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.category-subcategory.categories.index') }}">
                            <i class="icon">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.24 2H5.34C3.15 2 2 3.15 2 5.33V7.23C2 9.41 3.15 10.56 5.33 10.56H7.23C9.41 10.56 10.56 9.41 10.56 7.23V5.33C10.57 3.15 9.42 2 7.24 2Z"
                                        fill="#292D32" />
                                    <path
                                        d="M18.6695 2H16.7695C14.5895 2 13.4395 3.15 13.4395 5.33V7.23C13.4395 9.41 14.5895 10.56 16.7695 10.56H18.6695C20.8495 10.56 21.9995 9.41 21.9995 7.23V5.33C21.9995 3.15 20.8495 2 18.6695 2Z"
                                        fill="#292D32" />
                                    <path
                                        d="M18.6695 13.4297H16.7695C14.5895 13.4297 13.4395 14.5797 13.4395 16.7597V18.6597C13.4395 20.8397 14.5895 21.9897 16.7695 21.9897H18.6695C20.8495 21.9897 21.9995 20.8397 21.9995 18.6597V16.7597C21.9995 14.5797 20.8495 13.4297 18.6695 13.4297Z"
                                        fill="#292D32" />
                                    <path
                                        d="M7.24 13.4297H5.34C3.15 13.4297 2 14.5797 2 16.7597V18.6597C2 20.8497 3.15 21.9997 5.33 21.9997H7.23C9.41 21.9997 10.56 20.8497 10.56 18.6697V16.7697C10.57 14.5797 9.42 13.4297 7.24 13.4297Z"
                                        fill="#292D32" />
                                </svg>
                            </i>
                            <span class="item-name">Categories</span>
                        </a>
                    </li>
                @endcan
                @can('subcategory.read')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.category-subcategory.subcategories.index') }}">
                            <i class="icon">
                                <svg fill="#000000" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                    viewBox="0 0 72 72" enable-background="new 0 0 72 72" xml:space="preserve">
                                    <g>
                                        <path
                                            d="M60.5,14.5H9.5c-1.104,0-2-0.896-2-2s0.896-2,2-2h51c1.104,0,2,0.896,2,2S61.604,14.5,60.5,14.5z" />
                                    </g>
                                    <g>
                                        <path
                                            d="M60.5,48.5H9.5c-1.104,0-2-0.896-2-2s0.896-2,2-2h51c1.104,0,2,0.896,2,2S61.604,48.5,60.5,48.5z" />
                                    </g>
                                    <g>
                                        <path
                                            d="M60.5,32.5H9.5c-1.104,0-2-0.896-2-2s0.896-2,2-2h51c1.104,0,2,0.896,2,2S61.604,32.5,60.5,32.5z" />
                                    </g>
                                </svg>
                            </i>
                            <span class="item-name">Subcategories</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
    <div class="sidebar-footer"></div>
</aside>
