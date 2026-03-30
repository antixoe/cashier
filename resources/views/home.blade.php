@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <style>
        .nav-tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
        .nav-tabs button { padding: 10px 18px; border: 2px solid #dc2626; background: #fff; color: #dc2626; font-weight: 700; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
        .nav-tabs button.active { background: #dc2626; color: #fff; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .management-section { background: #fff7ed; padding: 20px; border-radius: 12px; border: 2px solid #fbbf24; }
    </style>

    <main class="main-content">
        <section class="glass-card">
            <h1>SupriMart Dashboard</h1>
            <p>Manage your POS system: products, cart, users, and roles all in one place.</p>
        </section>

        <!-- Tab Navigation -->
        <div class="nav-tabs">
            <button class="active" onclick="switchTab('products')">Products & POS</button>
            <button onclick="switchTab('users')">Manage Users</button>
            <button onclick="switchTab('roles')">Manage Roles</button>
            <button onclick="switchTab('sales')">Sales History</button>
        </div>

        <!-- Products & POS Tab -->
        <div id="products" class="tab-content active">
            <section class="glass-card" style="display: grid; grid-template-columns: 1fr 360px; gap: 20px;">
                <div>
                    <h2 style="margin-top:0;">Products</h2>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr><th>Name</th><th>Price</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td><button class="btn" onclick="addToCart({{ $product->id }})">Add to Cart</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h2 style="margin-top:0;">Shopping Cart</h2>
                    <div class="glass-card" style="padding: 16px; margin-bottom: 14px;">
                        <div id="cartArea">
                            @if(count($cartData) > 0)
                                <table>
                                    <thead>
                                    <tr><th>Product</th><th>Qty</th><th>Price</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                    @php $total = 0; @endphp
                                    @foreach($cartData as $productId => $item)
                                        @php $line = $item['price'] * $item['quantity']; $total += $line; @endphp
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>Rp {{ number_format($line, 0, ',', '.') }}</td>
                                            <td><button class="btn btn-danger" style="font-size:12px; padding: 4px 8px;" onclick="removeFromCart({{ $productId }})">-</button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <p class="text-right" style="margin:10px 0; font-weight: 700;">Total: Rp {{ number_format($total, 0, ',', '.') }}</p>
                            @else
                                <p style="margin: 0;">Cart is empty.</p>
                            @endif
                        </div>
                        <button class="btn" style="width:100%;" onclick="checkout()">Checkout</button>
                        <p id="checkoutMessage" style="margin-top: 10px; color: #f8fafc;"></p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Users Management Tab -->
        <div id="users" class="tab-content">
            <section class="management-section">
                <h2 style="margin-top: 0; color: #dc2626;">Manage Users</h2>
                <div style="margin-bottom: 20px;">
                    <button class="btn" style="background: #dc2626; color: #fff;" onclick="document.getElementById('addUserModal')?.style.display !== 'none' ? (document.getElementById('addUserModal').style.display = 'none') : (document.getElementById('addUserModal').style.display = 'flex')">+ Add New User</button>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Name</th><th>Email</th><th>Roles</th><th>Actions</th></tr>
                        </thead>
                        <tbody id="usersTable">
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->roles->pluck('name')->join(', ') ?: 'N/A' }}</td>
                                    <td>
                                        <button class="btn" style="padding: 4px 8px; font-size: 12px; background: #3b82f6;" onclick="viewUser({{ $user->id }})">View</button>
                                        <button class="btn" style="padding: 4px 8px; font-size: 12px; background: #f59e0b;" onclick="editUser({{ $user->id }})">Edit</button>
                                        <button class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;" onclick="deleteUser({{ $user->id }})">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center;">No users found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Roles Management Tab -->
        <div id="roles" class="tab-content">
            <section class="management-section">
                <h2 style="margin-top: 0; color: #dc2626;">Manage Roles</h2>
                <div style="margin-bottom: 20px;">
                    <button class="btn" style="background: #dc2626; color: #fff;" onclick="document.getElementById('addRoleModal')?.style.display !== 'none' ? (document.getElementById('addRoleModal').style.display = 'none') : (document.getElementById('addRoleModal').style.display = 'flex')">+ Add New Role</button>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Role Name</th><th>User Count</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->users->count() }} users</td>
                                    <td>
                                        <button class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;" onclick="deleteRole({{ $role->id }})">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align: center;">No roles found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Sales History Tab -->
        <div id="sales" class="tab-content">
            <section class="management-section">
                <h2 style="margin-top: 0; color: #dc2626;">Sales History</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Sale ID</th><th>Items</th><th>Total</th><th>Date</th><th>User</th></tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>#{{ $sale->id }}</td>
                                    <td>{{ $sale->saleItems->count() }} item(s)</td>
                                    <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $sale->user->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align: center;">No sales found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            // Remove active class from all buttons
            document.querySelectorAll('.nav-tabs button').forEach(btn => btn.classList.remove('active'));
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function csrfHeaders() {
            return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json' };
        }

        function addToCart(id) {
            fetch('{{ route('pos.addToCart') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({ product_id: id }) }).then(() => location.reload());
        }

        function removeFromCart(id) {
            fetch('{{ route('pos.removeFromCart') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({ product_id: id }) }).then(() => location.reload());
        }

        function checkout() {
            fetch('{{ route('pos.checkout') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({}) }).then(r => r.json()).then(data => {
                const el = document.getElementById('checkoutMessage');
                if (data.success) { el.innerText = 'Checkout successful: Rp ' + parseFloat(data.total).toFixed(0); setTimeout(() => location.reload(), 1000); }
                else { el.innerText = data.message || 'Checkout failed'; }
            });
        }

        function viewUser(id) {
            fetch('/management/users/' + id).then(r => r.json()).then(user => {
                alert(`User: ${user.name}\nEmail: ${user.email}\nRoles: ${user.roles.map(r => r.name).join(', ') || 'None'}`);
            });
        }

        function editUser(id) {
            const newName = prompt('Enter new name:');
            if (!newName) return;
            fetch('/management/users/' + id, { 
                method: 'PUT', 
                headers: csrfHeaders(), 
                body: JSON.stringify({ name: newName }) 
            }).then(() => location.reload());
        }

        function deleteUser(id) {
            if (!confirm('Are you sure?')) return;
            fetch('/management/users/' + id, { method: 'DELETE', headers: csrfHeaders() }).then(() => location.reload());
        }

        function deleteRole(id) {
            if (!confirm('Are you sure?')) return;
            fetch('/management/roles/' + id, { method: 'DELETE', headers: csrfHeaders() }).then(() => location.reload());
        }
    </script>
@endsection