<nav class="top-nav">
    <div class="nav-left">
        {{-- Unified Sidebar Toggle Button --}}
        @auth
            @if(Auth::user()->role === 'admin')
                <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            @endif
        @endauth

        <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="{{ asset('images/logo.jpg') }}" alt="DataCenter Logo"
                style="height: 60px; width: 120px; border-radius: 50%; border: 0 solid white;">
        </a>
    </div>

    <div class="nav-center">
        <a href="{{ route('home') }}"
            style="{{ request()->routeIs('home') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }}">
            {{ __('Home') }}
        </a>

        <a href="{{ route('rules') }}"
            style="{{ request()->routeIs('rules') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }}">
            {{ __('Rules') }}
        </a>

        @auth
            @if (Auth::user()->role == 'admin')
                {{-- Admin Panel link removed as they have Sidebar --}}
            @elseif (Auth::user()->role == 'manager')
                <a href="{{ route('manager.dashboard') }}"
                    style="{{ request()->routeIs('manager.*') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }}">
                    {{ __('Manager Area') }}
                </a>

            @elseif(Auth::user()->role == 'internal' || (Auth::user()->role == 'user' && Auth::user()->is_active))
                <a href="{{ route('internal.dashboard') }}"
                    style="{{ request()->routeIs('internal.*') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }}">
                    {{ __('My Area') }}
                </a>
            @endif

            @if(Auth::user()->role !== 'admin')
                <a href="{{ route('resources.index') }}"
                    style="{{ request()->routeIs('resources.*') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }}">
                    Inventory
                </a>

                @if(Auth::user()->canAccessMaintenance())
                    <a href="{{ route('maintenances.index') }}"
                        style="{{ request()->routeIs('maintenances.*') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }}">
                        Maintenance
                    </a>
                @endif
            @endif
        @endauth
    </div>

    <div class="nav-right">
        <div style="margin-right: 20px; font-size: 0.9em; display:flex; align-items:center;">
            <a href="{{ route('lang.switch', 'fr') }}"
                style="margin: 0; padding:0; border:none; {{ app()->getLocale() == 'fr' ? 'font-weight:bold; color:white;' : 'color:#bdc3c7; font-weight:normal;' }}">FR</a>
            <span style="color: white; opacity: 0.3; margin: 0 5px;">|</span>
            <a href="{{ route('lang.switch', 'en') }}"
                style="margin: 0; padding:0; border:none; {{ app()->getLocale() == 'en' ? 'font-weight:bold; color:white;' : 'color:#bdc3c7; font-weight:normal;' }}">EN</a>
        </div>

        @auth
                {{-- Notifications Bell Dropdown --}}
                <?php 
                                            $unreadCount = 0;
            try {
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', 0)->count();
            } catch (\Exception $e) {
            }
                                        ?>
                <div class="notification-bell-wrapper">
                    <button class="notification-bell-btn" onclick="openNotificationDropdown()" title="Notifications">
                        <i class="fas fa-bell"></i>
                        @if($unreadCount > 0)
                            <span class="notification-badge">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    @include('partials.notification_dropdown')
                </div>

                {{-- Report Incident for Managers Only --}}
                @if(Auth::user()->role === 'manager')
                    <a href="{{ route('incidents.create') }}" title="Report Incident"
                        style="color: #fbc531; text-decoration: none; font-weight: 500;">
                        Report
                    </a>
                @endif

                @if(Auth::user()->role !== 'admin')
                    <a href="{{ route('profile') }}"
                        style="{{ request()->routeIs('profile') ? 'border-bottom: 2px solid #3498db; color: #3498db;' : '' }} display: flex; align-items: center; gap: 8px;">

                        @if (Auth::user()->profile_photo_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="Profile"
                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #ecf0f1;">
                        @endif
                        <span>{{ __('Profile') }}</span>
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit"
                        style="background: none; border: none; color: white; cursor: pointer; text-decoration: none;">
                        {{ __('Logout') }}
                    </button>
                </form>
        @else
            <a href="{{ route('login') }}" style="{{ request()->routeIs('login') ? 'color: #3498db;' : '' }}">
                {{ __('Login') }}
            </a>
            <a href="{{ route('register') }}"
                style="background: #3498db; color: white; padding: 8px 15px; border-radius: 4px; border:none; margin-left: 10px; {{ request()->routeIs('register') ? 'background: #2980b9;' : '' }}">
                {{ __('Register') }}
            </a>
        @endauth
    </div>
</nav>