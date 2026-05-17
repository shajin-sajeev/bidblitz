<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bid-Blitz - Realtime Player Auction</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hide-number-arrows.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/theme.js') }}" defer></script>
    <script src="{{ asset('js/modal.js') }}" defer></script>
</head>
<body class="@yield('body_class')">

    <nav class="navbar">
        <div class="container">
            @auth
                <button type="button" class="mobile-menu-toggle" aria-label="Open menu" aria-controls="mobile-app-drawer" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            @endauth
            <a href="/" class="navbar-brand">⚡ Bid-Blitz</a>
            <div class="nav-links" style="display: flex; align-items: center; gap: 1rem;">
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link nav-dashboard-button {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="nav-action-label">Dashboard</span>
                    </a>
                    <a href="{{ route('profile.show') }}" class="nav-link nav-profile-button {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <span class="nav-action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M4.5 21a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="nav-action-label">Profile</span>
                    </a>
                    
                    <!-- Profile Picture in Navbar -->
                    <div class="navbar-profile" style="position: relative;">
                        @if(auth()->user()->profile_image)
                            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" 
                                 class="navbar-avatar">
                        @else
                            <div class="navbar-avatar navbar-avatar-default">
                                👤
                            </div>
                        @endif
                        <div class="profile-dropdown">
                            <div class="profile-dropdown-header">
                                @if(auth()->user()->profile_image)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="dropdown-avatar">
                                @else
                                    <div class="dropdown-avatar dropdown-avatar-default">👤</div>
                                @endif
                                <div class="dropdown-info">
                                    <div class="dropdown-name">{{ auth()->user()->name ?? 'User' }}</div>
                                    <div class="dropdown-email">{{ auth()->user()->email ?? auth()->user()->phone }}</div>
                                </div>
                            </div>
                            <div class="profile-dropdown-menu">
                                <a href="{{ route('profile.show') }}" class="dropdown-item">
                                    👤 My Profile
                                </a>
                                <a href="{{ route('settings.index') }}" class="dropdown-item">
                                    🎨 Theme Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item dropdown-logout">
                                    🚪 Logout
                                </a>
                            </div>
                        </div>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    @auth
        <div class="mobile-drawer-overlay" data-mobile-drawer-close></div>
        <aside id="mobile-app-drawer" class="mobile-app-drawer" aria-hidden="true">
            <div class="mobile-drawer-header">
                <div>
                    <div class="mobile-drawer-brand">Bid-Blitz</div>
                    <div class="mobile-drawer-subtitle">{{ auth()->user()->name ?? auth()->user()->email ?? 'Dashboard' }}</div>
                </div>
                <button type="button" class="mobile-drawer-close" aria-label="Close menu" data-mobile-drawer-close>&times;</button>
            </div>

            <nav class="mobile-drawer-nav" aria-label="Mobile application menu">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Overview</a>
                <a href="{{ route('auctions.create') }}" class="{{ request()->routeIs('auctions.create') ? 'active' : '' }}">Create Auction</a>
                <a href="{{ route('auctions.join') }}" class="{{ request()->routeIs('auctions.join') ? 'active' : '' }}">Join Auction</a>
                <a href="{{ route('auctions.joined') }}" class="{{ request()->routeIs('auctions.joined') ? 'active' : '' }}">Joined Auctions</a>
                <a href="{{ route('auctions.history') }}" class="{{ request()->routeIs('auctions.history') ? 'active' : '' }}">Auction History</a>
                <a href="{{ route('teams.joined') }}" class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">Joined Teams</a>
                <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.show') ? 'active' : '' }}">Profile</a>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">Settings</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="mobile-drawer-logout">
                @csrf
                <button type="submit" class="btn btn-outline">Logout</button>
            </form>
        </aside>
    @endauth

    <main class="container">
        @yield('content')
    </main>

    @php
        $flashMessage = null;

        if (session('error')) {
            $flashMessage = [
                'type' => 'error',
                'title' => 'Error',
                'message' => session('error'),
                'html' => false,
            ];
        } elseif (session('warning')) {
            $flashMessage = [
                'type' => 'warning',
                'title' => 'Warning',
                'message' => session('warning'),
                'html' => false,
            ];
        } elseif ($errors->any()) {
            $flashMessage = [
                'type' => 'error',
                'title' => 'Please fix these issues',
                'message' => implode("\n", $errors->all()),
                'html' => false,
            ];
        }
    @endphp

    @if($flashMessage)
        <script>
            window.appFlashMessage = @json($flashMessage);
        </script>
    @endif

    <!-- Modal System -->
    @include('partials.modal')

    @yield('scripts')
</body>
</html>
