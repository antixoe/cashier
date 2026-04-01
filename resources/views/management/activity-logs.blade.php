@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1>Activity Logs</h1>
                <p>View system activity and user actions.</p>
            </div>
            <a href="{{ route('management.activity-logs.export', ['filter' => $filter]) }}" class="btn">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </section>

        <section class="glass-card">
            <h2 style="margin-top:0;">Filter & Search</h2>
            <form method="GET" action="{{ route('management.activity-logs') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label>Filter by Action</label>
                    <select name="filter" class="glass-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ $filter === $action ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label>Search</label>
                    <input type="text" name="search" class="glass-input" placeholder="User name or description..." value="{{ $search }}">
                </div>
                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <button type="submit" class="btn">Search</button>
                    <a href="{{ route('management.activity-logs') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </section>

        <section class="glass-card">
            <h2 style="margin-top:0;">Recent Activities</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Model</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($log->user)
                                        <a href="{{ route('management.user-activity-logs', $log->user->id) }}" style="color: #fde68a; text-decoration: none;">
                                            {{ $log->user->name }}
                                        </a>
                                    @else
                                        <span style="color: #9ca3af;">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="display:inline-block;background:rgba(220,38,38,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td>
                                    @if($log->model_type)
                                        <span style="display:inline-block;background:rgba(245,158,11,0.35);color:#fef9c3;border-radius:999px;padding:2px 8px;font-size:12px;">
                                            {{ $log->model_type }}
                                        </span>
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: #9ca3af;">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:#fde68a;">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($activityLogs->hasPages())
                <div style="margin-top:12px;">{{ $activityLogs->links() }}</div>
            @endif
        </section>

        <section class="glass-card" style="margin-top: 20px;">
            <h3>Activity Summary</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                @php
                    $totalLogs = \App\Models\ActivityLog::count();
                    $todayLogs = \App\Models\ActivityLog::whereDate('created_at', today())->count();
                    $checkoutCount = \App\Models\ActivityLog::where('action', 'checkout')->count();
                    $loginCount = \App\Models\ActivityLog::where('action', 'login')->count();
                @endphp
                <div style="background: rgba(220, 38, 38, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(220, 38, 38, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Total Logs</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">{{ $totalLogs }}</div>
                </div>
                <div style="background: rgba(168, 85, 247, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(168, 85, 247, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Today</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">{{ $todayLogs }}</div>
                </div>
                <div style="background: rgba(34, 197, 94, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(34, 197, 94, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Checkouts</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">{{ $checkoutCount }}</div>
                </div>
                <div style="background: rgba(59, 130, 246, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(59, 130, 246, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Logins</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">{{ $loginCount }}</div>
                </div>
            </div>
        </section>
    </main>

@endsection
