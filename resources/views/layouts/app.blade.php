<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Issuing Asset System (IAS)') }}</title>

    <!-- Fonts and Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- jQuery should be loaded first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Vite Scripts (app.css and app.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    <div class="container-fluid mb-3">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                <!-- Sidebar Logo -->
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <span class="menu-label ms-2">American University of Phnom Penh</span>
                </div>
            
                <!-- Toggle Button -->
                <button id="sidebarToggle" class="btn">
                    <i class="fas fa-bars"></i>
                </button>
            
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link text-white" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> <span class="menu-label">Dashboard</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('profile.show') }}">
                                <i class="fas fa-user"></i> <span class="menu-label">Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> <span class="menu-label">Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('assets.index') }}">
                                <i class="fas fa-box"></i> <span class="menu-label">Assets</span>
                            </a>
                        </li>
            
                        <!-- Settings with Dropdown -->
                        <li class="nav-item">
                            <a class="nav-link text-white dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog"></i> <span class="menu-label">Settings</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="settingsDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('brands.index') }}">
                                        <i class="fas fa-tags"></i> <span class="menu-label ms-2">Brands</span>
                                    </a>
                                </li>
                                @if (auth()->user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('companies.index') }}">
                                            <i class="fas fa-building"></i> <span class="menu-label ms-2">Companies</span>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('device_types.index') }}">
                                        <i class="fas fa-desktop"></i> <span class="menu-label ms-2">Device Types</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('positions.index') }}">
                                        <i class="fas fa-briefcase"></i> <span class="menu-label ms-2">Positions</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('about') }}">
                                <i class="fas fa-info-circle"></i> <span class="menu-label">About</span>
                            </a>
                        </li>
            
                        <!-- Logout Button -->
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link text-white btn btn-link" style="text-decoration: none;">
                                    <i class="fas fa-sign-out-alt"></i> <span class="menu-label">Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            

            <!-- Main content -->
            <div id="content-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-0">
                <!-- Welcome Message -->
                <div class="dashboard-welcome">
                    <h1 class="h2 text-white text-center">
                        Issuing Asset System (IAS)
                    </h1>
                </div>

                <!-- Page-specific content -->
                <div class="px-4 bodycontent">

                    <!-- Display Error/Success Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Any page-specific scripts -->
    @yield('scripts')

    <script>
        window.assetRoutes = {};
    </script>
    
</body>
</html>
