@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1> User Management</h1>
                <p>View users in table, add new users via modal form.</p>
            </div>
            <button class="btn" onclick="openUserModal()">+ Add User</button>
        </section>

        @if(session('success'))
            <div class="glass-card" style="background: rgba(94, 30, 234, 0.35); border-color: rgba(192, 132, 252, 0.4);">
                <p class="message" style="color:#fde68a; margin: 0;">{{ session('success') }}</p>
            </div>
        @endif

        <section class="glass-card">
            <h2 style="margin-top:0;">User List</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Roles</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{!! $user->roles->map(fn($role) => '<span style="display:inline-block;background:rgba(245,158,11,0.35);color:#fef9c3;border-radius:999px;padding:2px 8px;font-size:12px;margin-right:4px;">'.$role->name.'</span>')->implode('') !!}</td>
                                <td>
                                    <button class="btn btn-secondary" style="font-size:12px; padding: 4px 8px; margin-right: 4px;" onclick="viewUser({{ $user->id }})">View</button>
                                    <button class="btn" style="font-size:12px; padding: 4px 8px; margin-right: 4px;" onclick="editUser({{ $user->id }})">Edit</button>
                                    <button class="btn btn-danger" style="font-size:12px; padding: 4px 8px;" onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center; color:#fde68a;">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages()) <div style="margin-top:12px;">{{ $users->links() }}</div> @endif
        </section>
    </main>

    <div class="modal-overlay" id="userModal">
        <div class="modal-content" style="max-width: 520px;">
            <span class="modal-close" onclick="closeUserModal()">&times;</span>
            <h2 style="margin: 0 0 18px 0; color: #fde68a;">Add New User</h2>
            <form id="userForm" action="{{ route('management.users.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:14px;"><label>Name</label><input type="text" name="name" class="glass-input" required></div>
                <div style="margin-bottom:14px;"><label>Email</label><input type="email" name="email" class="glass-input" required></div>
                <div style="margin-bottom:14px;"><label>Password</label><div style="position: relative;"><input type="password" name="password" class="glass-input" required id="userPassword"><button type="button" onclick="togglePasswordVisibility('userPassword')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #fde68a; cursor: pointer; font-size: 16px;">👁️</button></div></div>
                <div style="margin-bottom:18px;"><label>Role</label><select name="role_id" class="glass-select"><option value="">-- No Role --</option>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></div>
                <div style="text-align:right;"><button type="submit" class="btn">Save User</button></div>
            </form>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal-overlay" id="viewUserModal">
        <div class="modal-content" style="max-width: 520px;">
            <span class="modal-close" onclick="closeViewUserModal()">&times;</span>
            <h2 style="margin: 0 0 18px 0; color: #fde68a;">User Details</h2>
            <div id="userDetails">
                <div style="margin-bottom:14px;"><strong>Name:</strong> <span id="viewUserName"></span></div>
                <div style="margin-bottom:14px;"><strong>Email:</strong> <span id="viewUserEmail"></span></div>
                <div style="margin-bottom:14px;"><strong>Roles:</strong> <span id="viewUserRoles"></span></div>
                <div style="margin-bottom:14px;"><strong>Created At:</strong> <span id="viewUserCreated"></span></div>
                <div style="margin-bottom:14px;"><strong>Last Updated:</strong> <span id="viewUserUpdated"></span></div>
            </div>
            <div style="text-align:right;"><button type="button" class="btn" onclick="closeViewUserModal()">Close</button></div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editUserModal">
        <div class="modal-content" style="max-width: 520px;">
            <span class="modal-close" onclick="closeEditUserModal()">&times;</span>
            <h2 style="margin: 0 0 18px 0; color: #fde68a;">Edit User</h2>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                <div style="margin-bottom:14px;"><label>Name</label><input type="text" id="editUserName" name="name" class="glass-input" required></div>
                <div style="margin-bottom:14px;"><label>Email</label><input type="email" id="editUserEmail" name="email" class="glass-input" required></div>
                <div style="margin-bottom:18px;"><label>Role</label><select id="editUserRole" name="role_id" class="glass-select"><option value="">-- No Role --</option>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></div>
                <div style="text-align:right;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditUserModal()" style="margin-right: 8px;">Cancel</button>
                    <button type="submit" class="btn">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal-overlay" id="deleteUserModal">
        <div class="modal-content" style="max-width: 420px;">
            <span class="modal-close" onclick="closeDeleteUserModal()">&times;</span>
            <h2 style="margin: 0 0 18px 0; color: #fde68a;">Delete User</h2>
            <p style="color: #1f2937; margin-bottom: 20px;">Are you sure you want to delete user "<strong id="deleteUserName"></strong>"? This action cannot be undone.</p>
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" id="deleteUserId" name="user_id">
                <div style="text-align:right;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteUserModal()" style="margin-right: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Existing modal functions
    function openUserModal() { document.getElementById('userModal').classList.add('active'); }
    function closeUserModal() { document.getElementById('userModal').classList.remove('active'); document.getElementById('userForm').reset(); }
    document.getElementById('userModal').addEventListener('click', function(e) { if(e.target === this) closeUserModal(); });

    // View User Modal Functions
    function viewUser(userId) {
        fetch(`/management/users/${userId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('viewUserName').textContent = data.user.name;
                document.getElementById('viewUserEmail').textContent = data.user.email;
                document.getElementById('viewUserRoles').innerHTML = data.user.roles.length > 0
                    ? data.user.roles.map(role => `<span style="display:inline-block;background:rgba(245,158,11,0.35);color:#fef9c3;border-radius:999px;padding:2px 8px;font-size:12px;margin-right:4px;">${role.name}</span>`).join('')
                    : 'No roles assigned';
                document.getElementById('viewUserCreated').textContent = new Date(data.user.created_at).toLocaleString();
                document.getElementById('viewUserUpdated').textContent = new Date(data.user.updated_at).toLocaleString();
                document.getElementById('viewUserModal').classList.add('active');
            }
        })
        .catch(error => {
            alert('Error loading user details');
        });
    }

    function closeViewUserModal() {
        document.getElementById('viewUserModal').classList.remove('active');
    }

    // Edit User Modal Functions
    function editUser(userId) {
        fetch(`/management/users/${userId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editUserId').value = data.user.id;
                document.getElementById('editUserName').value = data.user.name;
                document.getElementById('editUserEmail').value = data.user.email;
                document.getElementById('editUserRole').value = data.user.roles.length > 0 ? data.user.roles[0].id : '';
                document.getElementById('editUserForm').action = `/management/users/${userId}`;
                document.getElementById('editUserModal').classList.add('active');
            }
        })
        .catch(error => {
            alert('Error loading user details');
        });
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.remove('active');
        document.getElementById('editUserForm').reset();
    }

    // Delete User Modal Functions
    function deleteUser(userId, userName) {
        document.getElementById('deleteUserId').value = userId;
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteUserForm').action = `/management/users/${userId}`;
        document.getElementById('deleteUserModal').classList.add('active');
    }

    function closeDeleteUserModal() {
        document.getElementById('deleteUserModal').classList.remove('active');
    }

    // Modal click outside to close
    document.getElementById('viewUserModal').addEventListener('click', function(e) { if(e.target === this) closeViewUserModal(); });
    document.getElementById('editUserModal').addEventListener('click', function(e) { if(e.target === this) closeEditUserModal(); });
    document.getElementById('deleteUserModal').addEventListener('click', function(e) { if(e.target === this) closeDeleteUserModal(); });

    // Password visibility toggle function
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '🙈';
        } else {
            input.type = 'password';
            button.textContent = '👁️';
        }
    }
</script>
@endsection
