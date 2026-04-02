<header class="navbar">
    <div class="brand">SupriMart</div>
    <nav>
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        @auth
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('pos.history') }}" class="{{ request()->routeIs('pos.history') ? 'active' : '' }}">Sales History</a>

            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('management.products.index') }}" class="{{ request()->routeIs('management.products*') ? 'active' : '' }}">Products</a>
                <a href="{{ route('management.categories') }}" class="{{ request()->routeIs('management.categories*') ? 'active' : '' }}">Categories</a>
                <a href="{{ route('management.users') }}" class="{{ request()->routeIs('management.users') ? 'active' : '' }}">Users</a>
                <a href="{{ route('management.roles') }}" class="{{ request()->routeIs('management.roles') ? 'active' : '' }}">Roles</a>
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports*') ? 'active' : '' }}">Reports</a>
                <a href="{{ route('management.activity-logs') }}" class="{{ request()->routeIs('management.activity-logs') ? 'active' : '' }}">Activity Logs</a>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'active' : '' }}">Settings</a>
            @endif
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
            <a href="{{ route('login') }}" class="btn" style="padding: 8px 12px; font-size: 14px;">Login</a>
        @endauth
    </div>
</header>
