@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1> Role Management</h1>
                <p>View roles in table, add new role via modal form.</p>
            </div>
            <button class="btn" onclick="openRoleModal()">+ Add Role</button>
        </section>

        @if(session('success'))
            <div class="glass-card" style="background: rgba(94, 30, 234, 0.35); border-color: rgba(192, 132, 252, 0.4);">
                <p class="message" style="color:#fde68a; margin: 0;">{{ session('success') }}</p>
            </div>
        @endif

        <section class="glass-card">
            <h2 style="margin-top:0;">Roles List</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Name</th><th>Description</th><th>User Count</th></tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td>{{ $role->users_count ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color:#fde68a;">No roles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($roles->hasPages()) <div style="margin-top:12px;">{{ $roles->links() }}</div> @endif
        </section>
    </main>

    <div class="modal-overlay" id="roleModal">
        <div class="modal-content" style="max-width: 520px;">
            <span class="modal-close" onclick="closeRoleModal()">&times;</span>
            <h2 style="margin: 0 0 18px 0; color: #fde68a;">Add New Role</h2>
            <form id="roleForm" action="{{ route('management.roles.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:14px;"><label>Role Name</label><input type="text" name="name" class="glass-input" required></div>
                <div style="margin-bottom:18px;"><label>Description</label><input type="text" name="description" class="glass-input"></div>
                <div style="text-align:right;"><button type="submit" class="btn">Save Role</button></div>
            </form>
        </div>
    </div>

    <script>
        function openRoleModal() { document.getElementById('roleModal').classList.add('active'); }
        function closeRoleModal() { document.getElementById('roleModal').classList.remove('active'); }
        document.getElementById('roleModal').addEventListener('click', function(e) { if(e.target === this) closeRoleModal(); });
    </script>
@endsection
