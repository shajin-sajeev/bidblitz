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
<body>

    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">⚡ Bid-Blitz</a>
            <div class="nav-links" style="display: flex; align-items: center; gap: 1rem;">
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                    
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
                                <a href="{{ route('settings.profile') }}" class="dropdown-item">
                                    ⚙️ Profile Settings
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

    <main class="container">
        @yield('content')
    </main>

    @php
        $flashMessage = null;

        if (session('success')) {
            $flashMessage = [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
                'html' => false,
            ];
        } elseif (session('error')) {
            $flashMessage = [
                'type' => 'error',
                'title' => 'Error',
                'message' => session('error'),
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

</body>
</html>
