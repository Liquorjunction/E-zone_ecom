<div class="iq-navbar-header" style="height: auto; min-height: 215px;">
    <div class="container-fluid iq-container">
        <div class="row">
            <div class="col-md-12">
                <div class="flex-wrap d-flex justify-content-between align-items-center">
                    <div>
                        <h1>@yield('header-title', 'Welcome!')</h1>
                        <p>@yield('header-description', 'To E-Zone Electronics Admin Panel.')</p>
                        {{-- Breadcrumbs --}}
                        @hasSection('breadcrumbs')
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @yield('breadcrumbs')
                                </ol>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
