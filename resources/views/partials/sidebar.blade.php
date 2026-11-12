@php
    $user = auth()->user();

    // Grouped navigation. Each item: [route name, icon, label].
    $sections = $user->isAdmin()
        ? [
            'Overview' => [
                ['admin.dashboard', 'fa-gauge-high', 'Dashboard'],
            ],
            'Operations' => [
                ['admin.bookings', 'fa-ticket', 'Bookings'],
                ['admin.trips', 'fa-calendar-days', 'Trips'],
                ['admin.routes', 'fa-route', 'Routes'],
                ['admin.buses', 'fa-bus', 'Buses'],
            ],
            'Finance' => [
                ['admin.transactions', 'fa-receipt', 'Transactions'],
            ],
            'Administration' => [
                ['admin.users', 'fa-user-shield', 'Users'],
                ['admin.logs', 'fa-clipboard-list', 'Activity Logs'],
                ['admin.settings', 'fa-gear', 'Settings'],
            ],
        ]
        : [
            'Overview' => [
                ['customer.dashboard', 'fa-gauge-high', 'Dashboard'],
            ],
            'My Travel' => [
                ['customer.bookings', 'fa-ticket', 'My Bookings'],
                ['customer.transactions', 'fa-receipt', 'My Transactions'],
            ],
            'Account' => [
                ['customer.profile', 'fa-user', 'Profile'],
            ],
        ];

    $roleLabel = $user->isAdmin() ? 'Administrator' : 'Customer · '.$user->user_type;
@endphp
<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-bus logo-icon"></i>
        <span>BusReserve</span>
        <button class="btn-ghost" id="sidebarToggle" title="Toggle sidebar" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></button>
    </div>

    <nav class="menu" aria-label="Main navigation">
        @foreach ($sections as $heading => $links)
            <div class="menu-section">
                <span class="menu-heading">{{ $heading }}</span>
                @foreach ($links as [$routeName, $icon, $label])
                    @php
                        $isActive = request()->routeIs($routeName, $routeName.'.*');
                    @endphp
                    <a href="{{ route($routeName) }}" @class(['active' => $isActive]) title="{{ $label }}" @if ($isActive) aria-current="page" @endif>
                        <i class="fas {{ $icon }}"></i><span>{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="sidebar-footer">
        <a href="{{ $user->isAdmin() ? route('admin.settings') : route('customer.profile') }}" class="sidebar-user" title="{{ $user->full_name }}">
            <span class="sidebar-avatar">{{ $user->initials() ?: '?' }}</span>
            <span class="sidebar-user-meta">
                <strong>{{ $user->full_name }}</strong>
                <small>{{ $roleLabel }}</small>
            </span>
        </a>
        <a class="logout" href="#" role="button"><i class="fas fa-right-from-bracket"></i><span>Logout</span></a>
    </div>
</aside>
