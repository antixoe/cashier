<header class="navbar">
    <div class="brand">SupriMart</div>
    <nav>
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        @auth
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('management.users') }}" class="{{ request()->routeIs('management.users') ? 'active' : '' }}">Users</a>
            <a href="{{ route('management.roles') }}" class="{{ request()->routeIs('management.roles') ? 'active' : '' }}">Roles</a>
        @endauth
    </nav>
    <div class="cta">
        @auth
            <span style="color:#fef3c7; font-weight:600;">Hi, {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn" style="padding: 8px 12px; font-size: 14px;">Logout</button>
            </form>
        @else
            <button class="btn" style="padding: 8px 12px; font-size: 14px;" onclick="openLoginModal()">Login</button>
        @endauth
    </div>
</header>
