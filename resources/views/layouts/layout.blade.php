<!doctype html>
<html lang="en" class=" layout-navbar-fixed layout-menu-fixed layout-compact " dir="ltr" data-skin="default"
    data-assets-path="{{ asset('asset') }}/assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>PROJECT POS MINIMARKET</title>

    <!-- Canonical SEO -->
    <meta name="description"
        content="Sneat is the best bootstrap 5 dashboard for responsive web apps. Streamline your app development process with ease." />

    <meta name="keywords"
        content="Sneat bootstrap dashboard, sneat bootstrap 5 dashboard, themeselection, html dashboard, web dashboard, frontend dashboard, responsive bootstrap theme" />
    <meta property="og:title" content="Sneat Bootstrap 5 Dashboard PRO by ThemeSelection" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="https://themeselection.com/item/sneat-dashboard-pro-bootstrap/" />
    <meta property="og:image"
        content="https://themeselection.com/wp-content/uploads/edd/2024/08/sneat-dashboard-pro-bootstrap-smm-image.png" />
    <meta property="og:description"
        content="Sneat is the best bootstrap 5 dashboard for responsive web apps. Streamline your app development process with ease." />
    <meta property="og:site_name" content="ThemeSelection" />
    <link rel="canonical" href="https://themeselection.com/item/sneat-dashboard-pro-bootstrap/" />

    <!-- ? PROD Only: Google Tag Manager (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5DDHKGP');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('asset') }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->


    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ asset('asset') }}/assets/css/demo.css" />


    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('asset') }}/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/libs/datatables-select-bs5/select.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('asset') }}/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('asset') }}/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css" />

    <!-- Page CSS -->

    @stack('style')
    <!-- Helpers -->
    <script src="{{ asset('asset') }}/assets/vendor/js/helpers.js"></script>

    <script src="{{ asset('asset') }}/assets/js/config.js"></script>

</head>

<body>

    <!-- ?PROD Only: Google Tag Manager (noscript) (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DDHKGP" height="0" width="0"
            style="display: none; visibility: hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar  ">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu">

                <div class="app-brand demo ">
                    <a href="index.html" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <span class="text-primary">

                                <svg width="25" viewBox="0 0 25 42" version="1.1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <defs>
                                        <path
                                            d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                                            id="path-1"></path>
                                        <path
                                            d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                                            id="path-3"></path>
                                        <path
                                            d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                                            id="path-4"></path>
                                        <path
                                            d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                                            id="path-5"></path>
                                    </defs>
                                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none"
                                        fill-rule="evenodd">
                                        <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                            <g id="Icon" transform="translate(27.000000, 15.000000)">
                                                <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                    <mask id="mask-2" fill="white">
                                                        <use xlink:href="#path-1"></use>
                                                    </mask>
                                                    <use fill="currentColor" xlink:href="#path-1"></use>
                                                    <g id="Path-3" mask="url(#mask-2)">
                                                        <use fill="currentColor" xlink:href="#path-3"></use>
                                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3">
                                                        </use>
                                                    </g>
                                                    <g id="Path-4" mask="url(#mask-2)">
                                                        <use fill="currentColor" xlink:href="#path-4"></use>
                                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4">
                                                        </use>
                                                    </g>
                                                </g>
                                                <g id="Triangle"
                                                    transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                                    <use fill="currentColor" xlink:href="#path-5"></use>
                                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5">
                                                    </use>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </span>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-2">Sneat</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="icon-base bx bx-chevron-left"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboards -->
                    <li class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                            <i class="menu-icon icon-base bx bx-home-smile"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>   
                    
                    {{-- <li class="menu-header small">
                        <span class="menu-header-text">Manajemen Produk</span>
                    </li> --}}

                    {{-- Manajemen Produk --}}
                    <li class="menu-item {{ Request::routeIs('kategori.index') || Request::routeIs('produk.index') || Request::routeIs('supplier.index') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon bx bx-store-alt"></i>
                            <div>Manajemen Produk</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ Request::routeIs('kategori.index') ? 'active' : '' }}">
                                <a href="{{ route('kategori.index') }}" class="menu-link">
                                    <div>Kategori Produk</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('produk.index') ? 'active' : '' }}">
                                <a href="{{ route('produk.index') }}" class="menu-link">
                                    <div>Produk</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('supplier.index') ? 'active' : '' }}">
                                <a href="{{ route('supplier.index') }}" class="menu-link">
                                    <div>Supplier</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('supplpenerimaan_barangier.index') ? 'active' : '' }}">
                                <a href="{{ route('penerimaan_barang.index') }}" class="menu-link">
                                    <div>Penerimaan Barang</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- <!-- Penerimaan Produk -->
                    <li class="menu-item {{ Request::routeIs('penerimaan_barang.index') ? 'active' : '' }}">
                        <a href="{{ route('penerimaan_barang.index') }}" class="menu-link">
                            <i class="menu-icon bx bx-receipt"></i>
                            <div>Penerimaan Barang</div>
                        </a>
                    </li>     --}}
                    
                    <li class="menu-header small">
                        <span class="menu-header-text">Manajemen Pelanggan</span>
                    </li>
                    
                    <!-- Manajemen Member -->
                    <li class="menu-item {{ Request::routeIs('member.index') ? 'active' : '' }}">
                        <a href="{{ route('member.index') }}" class="menu-link">
                            <i class="menu-icon bx bx-id-card"></i>
                            <div>Member</div>
                        </a>
                    </li>     
                    
                    <!-- Manajemen Penjualan -->
                    <li class="menu-item {{ Request::routeIs('penjualan.index') || Request::routeIs('penjualan.create') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon bx bx-cart-alt"></i>
                            <div>Penjualan</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ Request::routeIs('penjualan.index') ? 'active' : '' }}">
                                <a href="{{ route('penjualan.index') }}" class="menu-link">
                                    <div>Penjualan Produk</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('penjualan.create') ? 'active' : '' }}">
                                <a href="{{ route('penjualan.create') }}" class="menu-link">
                                    <div>Tambah Penjualan</div>
                                </a>
                            </li>
                        </ul>
                    </li>  

                    <li class="menu-item {{ Request::routeIs('pembayaran.index') ? 'active' : '' }}">
                        <a href="{{ route('pembayaran.index') }}" class="menu-link">
                            <i class="menu-icon bx bx-money"></i>
                            <div>Pembayaran</div>
                        </a>
                    </li> 
                    
                    <!-- Manajemen Laporan -->
                    <li class="menu-item {{ Request::routeIs('laporan.penjualan') || Request::routeIs('laporan.transaksi') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon bx bx-file"></i>
                            <div>Laporan</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ Request::routeIs('laporan.penjualan') ? 'active' : '' }}">
                                <a href="{{ route('laporan.penjualan') }}" class="menu-link">
                                    <div>Penjualan Produk</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('laporan.transaksi') ? 'active' : '' }}">
                                <a href="{{ route('laporan.transaksi') }}" class="menu-link">
                                    <div>Transaksi Produk</div>
                                </a>
                            </li>
                        </ul>
                    </li> 

                    <li class="menu-header small">
                        <span class="menu-header-text">Manajemen Pengguna</span>
                    </li>

                    <!-- Manajemen Pengguna -->
                    <li class="menu-item {{ Request::routeIs('users.index') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="menu-link">
                            <i class="menu-icon bx bx-user-circle"></i>
                            <div>Pengguna</div>
                        </a>
                    </li>     
            </aside>

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);"
                    class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="bx bx-menu icon-base"></i>
                    <i class="bx bx-chevron-right icon-base"></i>
                </a>
            </div>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">

                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0   d-xl-none ">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base bx bx-menu icon-md"></i>
                        </a>
                    </div>


                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item navbar-search-wrapper mb-0">
                                <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                                    <span class="d-inline-block text-body-secondary fw-normal"
                                        id="autocomplete"></span>
                                </a>
                            </div>
                        </div>

                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

                            <!-- Style Switcher -->
                            <li class="nav-item dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme"
                                    href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
                                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center active"
                                            data-bs-theme-value="light" aria-pressed="false">
                                            <span><i class="icon-base bx bx-sun icon-md me-3"
                                                    data-icon="sun"></i>Light</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center"
                                            data-bs-theme-value="dark" aria-pressed="true">
                                            <span><i class="icon-base bx bx-moon icon-md me-3"
                                                    data-icon="moon"></i>Dark</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center"
                                            data-bs-theme-value="system" aria-pressed="false">
                                            <span><i class="icon-base bx bx-desktop icon-md me-3"
                                                    data-icon="desktop"></i>System</span>
                                        </button>
                                    </li>
                                </ul>
                            </li>
                            <!-- / Style Switcher-->

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('asset') }}/assets/img/avatars/1.png" alt
                                            class="rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="pages-account-settings-account.html">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('asset') }}/assets/img/avatars/1.png" alt
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{Auth::user()->name}}</h6>
                                                    <small class="text-body-secondary">{{Auth::user()->role}}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    
                                    <li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="icon-base bx bx-power-off icon-md me-3"></i>
                                            <span>Log Out</span>
                                        </a>
                                    </li>
                                    
                                </ul>
                            </li>
                            <!--/ User -->

                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        @yield('content')

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>, made with ❤️ by <a href="https://themeselection.com"
                                        target="_blank" class="footer-link">ThemeSelection</a>
                                </div>
                                <div class="d-none d-lg-inline-block">

                                    <a href="https://themeselection.com/license/" class="footer-link me-4"
                                        target="_blank">License</a>
                                    <a href="https://themeselection.com/" target="_blank"
                                        class="footer-link me-4">More Themes</a>

                                    <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/documentation/"
                                        target="_blank" class="footer-link me-4">Documentation</a>


                                    <a href="https://themeselection.com/support/" target="_blank"
                                        class="footer-link d-none d-sm-inline-block">Support</a>

                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->


                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>


        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>

    </div>
    <!-- / Layout wrapper -->

    <script src="{{ asset('asset') }}/assets/vendor/libs/jquery/jquery.js"></script>

    <script src="{{ asset('asset') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('asset') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('asset') }}/assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="{{ asset('asset') }}/assets/vendor/libs/pickr/pickr.js"></script>


    <script src="{{ asset('asset') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>


    <script src="{{ asset('asset') }}/assets/vendor/libs/hammer/hammer.js"></script>

    <script src="{{ asset('asset') }}/assets/vendor/libs/i18n/i18n.js"></script>


    <script src="{{ asset('asset') }}/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    {{-- <!-- Vendors JS --> --}}
    <script src="{{ asset('asset') }}/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->

    <script src="{{ asset('asset') }}/assets/js/main.js"></script>


    <!-- Page JS -->
    <script src="{{ asset('asset') }}/assets/js/tables-datatables-extensions.js"></script>
    <script src="{{ asset('asset') }}/assets/js/tables-datatables-basic.js"></script>

    @stack('script')
</body>

</html>
