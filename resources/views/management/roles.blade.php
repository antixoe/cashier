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
                        <tr><th>Name</th><th>Description</th><th>User Count</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td>{{ $role->users_count ?? 0 }}</td>
                                <td>
                                    <button class="btn" style="padding:4px 8px; margin-right:6px;" onclick="openEditRoleModal({{ $role->id }})">Edit</button>
                                    <button class="btn btn-danger" style="padding:4px 8px;" onclick="deleteRole({{ $role->id }})">Delete</button>
                                </td>
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

    <div class="modal-overlay" id="editRoleModal">
        <div class="modal-content" style="max-width: 520px;">
            <span class="modal-close" onclick="closeEditRoleModal()">&times;</span>
            <h2 style="margin: 0 0 18px 0; color: #fde68a;">Edit Role</h2>
            <form id="editRoleForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editRoleId" name="role_id" value="">
                <div style="margin-bottom:14px;"><label>Role Name</label><input type="text" id="editRoleName" name="name" class="glass-input" required></div>
                <div style="margin-bottom:18px;"><label>Description</label><input type="text" id="editRoleDescription" name="description" class="glass-input"></div>
                <div style="text-align:right;"><button type="button" class="btn" onclick="submitEditRole()">Update Role</button></div>
            </form>
        </div>
    </div>

    <script>
        function openRoleModal() { document.getElementById('roleModal').classList.add('active'); }
        function closeRoleModal() { document.getElementById('roleModal').classList.remove('active'); }

        function openEditRoleModal(id) {
            fetch('{{ route('management.roles.show', 'ID') }}'.replace('ID', id), {
                headers: { 'Accept': 'application/json' },
            })
            .then(response => response.json())
            .then(role => {
                document.getElementById('editRoleId').value = role.id;
                document.getElementById('editRoleName').value = role.name;
                document.getElementById('editRoleDescription').value = role.description || '';
                document.getElementById('editRoleModal').classList.add('active');
            })
            .catch(err => { console.error(err); alert('Failed to load role data.'); });
        }

        function closeEditRoleModal() { document.getElementById('editRoleModal').classList.remove('active'); }

        function submitEditRole() {
            const id = document.getElementById('editRoleId').value;
            const name = document.getElementById('editRoleName').value.trim();
            const description = document.getElementById('editRoleDescription').value.trim();

            if (!name) {
                alert('Name is required');
                return;
            }

            fetch('{{ route('management.roles.update', 'ID') }}'.replace('ID', id), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, description })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Role updated successfully');
                    closeEditRoleModal();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update role');
                }
            })
            .catch(err => { console.error(err); alert('Failed to update role.'); });
        }

        function deleteRole(id) {
            if (!confirm('Delete this role?')) return;

            fetch('{{ route('management.roles.destroy', 'ID') }}'.replace('ID', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Role deleted successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete role');
                }
            })
            .catch(err => { console.error(err); alert('Failed to delete role.'); });
        }

        document.getElementById('roleModal').addEventListener('click', function(e) { if(e.target === this) closeRoleModal(); });
        document.getElementById('editRoleModal').addEventListener('click', function(e) { if(e.target === this) closeEditRoleModal(); });
    </script>
@endsection
