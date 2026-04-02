<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupriMart - Modern POS System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, rgba(255,255,255,0.26), rgba(253, 224, 71, 0.28)), radial-gradient(circle at 20% 20%, #fed7aa 0%, #fdba74 45%, #dc2626 100%);
            color: #1f2937;
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 48px;
            background: rgba(220, 38, 38, 0.85);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.25);
            z-index: 1000;
        }
        .navbar .brand { font-size: 28px; font-weight: 800; color: #fef3c7; letter-spacing: 1px; }
        .navbar nav { display: flex; gap: 32px; }
        .navbar nav a { color: #fef3c7; text-decoration: none; font-weight: 600; transition: all 0.2s; }
        .navbar nav a:hover { color: #fde68a; }
        .navbar .cta { display: flex; gap: 12px; }
        .modal-overlay {
            pointer-events: auto !important;
            z-index: 99999 !important;
        }
        .modal-content {
            pointer-events: auto !important;
            z-index: 100000 !important;
        }
        .modal-content form, .modal-content button, .modal-content input {
            pointer-events: auto !important;
        }
        .btn { border: none; color: #ffffff; font-weight: 700; border-radius: 14px; cursor: pointer; padding: 12px 24px; background: linear-gradient(155deg, rgba(220, 38, 38, 0.95), rgba(239, 68, 68, 0.9)); box-shadow: 0 8px 18px rgba(185, 28, 28, 0.5); transition: transform 0.2s, filter 0.2s; text-decoration: none; display: inline-block; }
        .btn:hover { transform: translateY(-2px); filter: brightness(1.05); }
        .btn-secondary { background: linear-gradient(155deg, rgba(245, 158, 11, 0.9), rgba(251, 191, 36, 0.85)); }

        .toast-notification {
            position: fixed;
            bottom: 18px;
            right: 18px;
            min-width: 240px;
            padding: 12px 16px;
            border-radius: 10px;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 10px 26px rgba(0,0,0,0.35);
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            z-index: 999999;
            display: none;
            pointer-events: none;
        }

        .toast-notification.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Hero Section */
        .hero {
            margin-top: 72px;
            padding: 100px 48px;
            text-align: center;
            color: #fff;
        }
        .hero h1 {
            font-size: 56px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            color: #fef3c7;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Content Sections */
        .content { padding: 80px 48px; }
        .section {
            margin-bottom: 80px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 60px;
            backdrop-filter: blur(12px);
        }
        .section h2 {
            font-size: 36px;
            color: #1f2937;
            margin-bottom: 40px;
            text-align: center;
        }
        .section p {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(220, 38, 38, 0.2);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(220, 38, 38, 0.2);
        }
        .feature-card i {
            font-size: 40px;
            color: #dc2626;
            margin-bottom: 16px;
        }
        .feature-card h3 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 12px;
        }
        .feature-card p {
            color: #6b7280;
            margin: 0;
        }

        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 32px;
            border-radius: 16px;
            text-align: center;
            border-left: 4px solid #dc2626;
        }
        .stat-box .number {
            font-size: 36px;
            font-weight: 800;
            color: #dc2626;
        }
        .stat-box .label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 8px;
        }

        /* About Section */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
        }
        .about-text h3 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 16px;
        }
        .about-text p {
            margin-bottom: 16px;
        }

        /* Footer */
        footer {
            background: rgba(30, 41, 59, 0.95);
            color: #f1f5f9;
            padding: 40px 48px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        footer p { margin: 8px 0; font-size: 14px; }
        footer .divider { height: 1px; background: rgba(255,255,255,0.1); margin: 20px 0; }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 0 24px; flex-direction: column; height: auto; }
            .navbar nav { display: none; }
            .hero { padding: 60px 24px; }
            .hero h1 { font-size: 36px; }
            .content, .section { padding: 40px 24px; }
            .about-content { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <div class="brand">SupriMart</div>
        <nav>
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="cta">
            @auth
                <a href="{{ route('pos.index') }}" class="btn">Dashboard</a>
            @else
                <a href="#" class="btn" onclick="event.preventDefault(); openLoginModal();">Login</a>
            @endauth
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to SupriMart</h1>
        <p>The modern, powerful POS system designed to streamline your business operations and maximize efficiency.</p>
        <div class="cta-buttons">
            @auth
                <a href="{{ route('pos.index') }}" class="btn">Go to Dashboard</a>
            @else
                <a href="#" class="btn" onclick="event.preventDefault(); openLoginModal();">Get Started</a>
                <a href="#features" class="btn btn-secondary">Learn More</a>
            @endauth
        </div>
    </section>

    <!-- Main Content -->
    <div class="content">
        <!-- Features Section -->
        <section id="features" class="section">
            <h2><i class="bi bi-lightning-charge"></i> Powerful Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="bi bi-cart-check"></i>
                    <h3>Smart POS System</h3>
                    <p>Fast and intuitive point-of-sale interface with real-time inventory tracking and cart management.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-box-seam"></i>
                    <h3>Product Catalog</h3>
                    <p>Browse and order products with pricing and availability shown instantly.</p>
                </div>
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                <div class="feature-card">
                    <i class="bi bi-bar-chart"></i>
                    <h3>Advanced Analytics</h3>
                    <p>Comprehensive reports and insights into your sales, revenue trends, and product performance.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-box-seam"></i>
                    <h3>Inventory Management</h3>
                    <p>Complete CRUD operations for products with price management and stock tracking.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-people"></i>
                    <h3>User Management</h3>
                    <p>Flexible user administration with role-based access control and permissions system.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-shield-check"></i>
                    <h3>Activity Logging</h3>
                    <p>Complete audit trail of all system activities with detailed logs and export capabilities.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-graph-up"></i>
                    <h3>Real-time Reporting</h3>
                    <p>Generate instant reports, track metrics, and export data for further analysis.</p>
                </div>
                @endif
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="section" style="background: rgba(220, 38, 38, 0.15);">
            <h2>Our Impact</h2>
            <div class="stats">
                <div class="stat-box">
                    <div class="number">500+</div>
                    <div class="label">Active Users</div>
                </div>
                <div class="stat-box">
                    <div class="number">10K+</div>
                    <div class="label">Transactions</div>
                </div>
                <div class="stat-box">
                    <div class="number">99.9%</div>
                    <div class="label">Uptime</div>
                </div>
                <div class="stat-box">
                    <div class="number">24/7</div>
                    <div class="label">Support</div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="section">
            <h2>About SupriMart</h2>
            <div class="about-content">
                <div class="about-text">
                    <h3>Your Complete Business Solution</h3>
                    <p>SupriMart is a comprehensive point-of-sale system built from the ground up to meet the needs of modern retail businesses.</p>
                    <p>With our intuitive interface, powerful analytics, and robust inventory management, you can focus on growing your business while we handle the transactions.</p>
                    <p>Whether you're a small boutique or a multi-location retailer, SupriMart scales with your business.</p>
                </div>
                <div class="about-text">
                    <h3>Why Choose SupriMart?</h3>
                    <p><strong><i class="bi bi-check-circle" style="color: #22c55e;"></i> Easy to Use:</strong> Intuitive design requires minimal training.</p>
                    <p><strong><i class="bi bi-check-circle" style="color: #22c55e;"></i> Secure:</strong> Enterprise-grade security for your data.</p>
                    <p><strong><i class="bi bi-check-circle" style="color: #22c55e;"></i> Reliable:</strong> Built on proven technology stack.</p>
                    <p><strong><i class="bi bi-check-circle" style="color: #22c55e;"></i> Scalable:</strong> Grows with your business needs.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="section" style="background: linear-gradient(135deg, rgba(220, 38, 38, 0.3), rgba(251, 146, 60, 0.3)); text-align: center;">
            <h2>Ready to Transform Your Business?</h2>
            <p style="font-size: 18px; margin-bottom: 32px;">Join hundreds of businesses using SupriMart for their POS needs.</p>
            @auth
                <a href="{{ route('pos.index') }}" class="btn">Go to Dashboard</a>
            @else
                <a href="#" class="btn" style="font-size: 16px; padding: 16px 32px;" onclick="event.preventDefault(); openLoginModal();">Get Started Now</a>
            @endauth
        </section>
    </div>

    <!-- Footer -->
    <footer id="contact">
        <h3 style="color: #fef3c7; margin-bottom: 20px;">SupriMart POS System</h3>
        <p>&copy; 2026 SupriMart. All rights reserved.</p>
        <p>Built with <i class="bi bi-heart-fill" style="color: #dc2626;"></i> for modern retailers</p>
        <div class="divider"></div>
        <p style="font-size: 12px; color: #94a3b8;">Contact: support@suprimart.local | Phone: +62-XXX-XXXX</p>
        <p style="font-size: 12px; color: #94a3b8;">Address: Indonesia | Email: info@suprimart.local</p>
    </footer>

    <div id="toastNotification" class="toast-notification"></div>

    <!-- Login Modal -->
    <div class="modal-overlay" id="loginModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 99999; pointer-events: auto;">
        <div style="background: rgba(255, 255, 255, 0.95); border-radius: 18px; padding: 40px; max-width: 420px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; pointer-events: auto;">
            <button type="button" onclick="closeLoginModal()" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 28px; color: #dc2626; cursor: pointer; padding: 0; width: 32px; height: 32px; pointer-events: auto;">×</button>
            <h2 style="color: #1f2937; margin: 0 0 24px 0; font-size: 26px;">Login</h2>
            <div id="loginMessage" style="margin-bottom: 16px;"></div>
            <form id="loginForm" method="POST" action="{{ route('login') }}" style="pointer-events: auto;">
                @csrf
                <div style="margin-bottom: 16px; pointer-events: auto;">
                    <label for="loginEmail" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 14px;">Email</label>
                    <input id="loginEmail" type="email" name="email" placeholder="your@email.com" required style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box; pointer-events: auto;">
                </div>
                <div style="margin-bottom: 20px; pointer-events: auto;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 14px;">Password</label>
                    <div style="position: relative; pointer-events: auto;">
                        <input id="loginPassword" type="password" name="password" placeholder="Enter password" required style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box; pointer-events: auto;">
                        <button type="button" onclick="toggleLoginPassword(event)" style="position: absolute; right: 10px; top: 10px; background: none; border: none; cursor: pointer; font-size: 18px; color: #666; pointer-events: auto;"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button type="submit" style="width: 100%; padding: 12px; background: #dc2626; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; pointer-events: auto;">Submit</button>
            </form>
        </div>
    </div>

    <!-- Add User Modal (Admin Only) -->
    @if(auth()->check() && auth()->user()->hasRole('admin'))
    <div class="modal-overlay" id="addUserModal" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 2000;">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 32px; color: #1f2937; max-width: 420px;">
            <span class="modal-close" onclick="document.getElementById('addUserModal').style.display = 'none'" style="position: absolute; right: 16px; top: 16px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3 style="margin: 0 0 24px 0; color: #dc2626; font-size: 22px;">Add New User</h3>
            <form id="addUserForm" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Name</label>
                    <input type="text" name="name" class="glass-input" placeholder="User Name" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email</label>
                    <input type="email" name="email" class="glass-input" placeholder="user@example.com" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Password</label>
                    <input type="password" name="password" class="glass-input" placeholder="••••••••" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Role</label>
                    <select name="role_id" class="glass-input" style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn" style="width: 100%; background: #dc2626; color: white; margin-top: 8px;" onclick="submitAddUser()">Add User</button>
            </form>
        </div>
    </div>
    @endif

    <!-- Add Role Modal (Admin Only) -->
    @if(auth()->check() && auth()->user()->hasRole('admin'))
    <div class="modal-overlay" id="addRoleModal" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 2000;">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 32px; color: #1f2937; max-width: 420px;">
            <span class="modal-close" onclick="document.getElementById('addRoleModal').style.display = 'none'" style="position: absolute; right: 16px; top: 16px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3 style="margin: 0 0 24px 0; color: #dc2626; font-size: 22px;">Add New Role</h3>
            <form id="addRoleForm" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Role Name</label>
                    <input type="text" name="name" class="glass-input" placeholder="Role Name" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>
                <button type="button" class="btn" style="width: 100%; background: #dc2626; color: white; margin-top: 8px;" onclick="submitAddRole()">Add Role</button>
            </form>
        </div>
    </div>
    @endif

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'flex';
            document.getElementById('loginEmail').focus();
        }
        
        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
            document.getElementById('loginForm').reset();
            document.getElementById('loginMessage').innerHTML = '';
        }
        
        function toggleLoginPassword(e) {
            e.preventDefault();
            const el = document.getElementById('loginPassword');
            el.type = el.type === 'password' ? 'text' : 'password';
        }
        
        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        @if(!empty($openLogin))
            openLoginModal();
        @endif
    </script>
</body>
</html>
                    </div>
                </div>

                <div>
                    <h2 style="margin-top:0;">Shopping Cart</h2>
                    <div class="glass-card" style="padding: 16px; margin-bottom: 14px;">
                        <div id="cartArea">
                            @if($cartData && count($cartData) > 0)
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
                                            <td>
                                                <button class="btn btn-danger" style="font-size:12px; padding: 4px 8px; margin-right:4px;" onclick="removeFromCart({{ $productId }})">-</button>
                                                <button class="btn" style="font-size:12px; padding: 4px 8px;" onclick="addToCart({{ $productId }})">+</button>
                                            </td>
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

        <!-- Users Management Tab (Admin Only) -->
        @if(auth()->check() && auth()->user()->hasRole('admin'))
        <div id="users" class="tab-content">
            <section class="management-section">
                <h2 style="margin-top: 0; color: #dc2626;">Manage Users</h2>
                <div style="margin-bottom: 20px;">
                    <button class="btn" style="background: #dc2626; color: #fff;" onclick="document.getElementById('addUserModal').style.display !== 'none' ? (document.getElementById('addUserModal').style.display = 'none') : (document.getElementById('addUserModal').style.display = 'flex')">+ Add New User</button>
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
                                    <td>{{ $role->users ? $role->users->count() : 0 }} users</td>
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
        @endif

        @if(auth()->check() && auth()->user()->hasRole('admin'))
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
                                    <td>{{ $sale->saleItems ? $sale->saleItems->count() : 0 }} item(s)</td>
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
        @endif
    </main>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editUserModal" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 2000;" onclick="if(event.target.id === 'editUserModal') { document.getElementById('editUserModal').style.display = 'none'; }">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 32px; color: #1f2937; max-width: 420px;">
            <span class="modal-close" onclick="document.getElementById('editUserModal').style.display = 'none'" style="position: absolute; right: 16px; top: 16px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3 style="margin: 0 0 24px 0; color: #dc2626; font-size: 22px;">Edit User</h3>
            <form id="editUserForm" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="editUserId" name="user_id" value="">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">User Name</label>
                    <input type="text" id="editUserName" name="name" class="glass-input" placeholder="User Name" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email</label>
                    <input type="email" id="editUserEmail" name="email" class="glass-input" placeholder="user@example.com" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>
                <button type="button" class="btn" style="width: 100%; background: #f59e0b; color: white; margin-top: 8px;" onclick="submitEditUser()">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal-overlay" id="viewUserModal" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 2000;" onclick="if(event.target.id === 'viewUserModal') { document.getElementById('viewUserModal').style.display = 'none'; }">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 32px; color: #1f2937; max-width: 420px;">
            <span class="modal-close" onclick="document.getElementById('viewUserModal').style.display = 'none'" style="position: absolute; right: 16px; top: 16px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3 style="margin: 0 0 24px 0; color: #dc2626; font-size: 22px;">User Details</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 6px;">USER NAME</div>
                    <div id="viewUserName" style="font-size: 16px; color: #1f2937; font-weight: 600;"></div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 6px;">EMAIL</div>
                    <div id="viewUserEmail" style="font-size: 14px; color: #4b5563;"></div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 6px;">ROLES</div>
                    <div id="viewUserRoles" style="font-size: 14px; color: #4b5563;"></div>
                </div>
            </div>
        </div>
    </div>

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

        function showToast(message, success = true) {
            const toast = document.getElementById('toastNotification');
            if (!toast) return;

            toast.textContent = message;
            toast.style.background = success ? 'linear-gradient(155deg, #059669, #10b981)' : 'linear-gradient(155deg, #dc2626, #ef4444)';
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3200);
        }

        function checkout() {
            fetch('{{ route('pos.checkout') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({}) }).then(r => r.json()).then(data => {
                const el = document.getElementById('checkoutMessage');
                if (data.success) {
                    const message = 'Checkout successful: Rp ' + parseFloat(data.total).toFixed(0);
                    if (el) el.innerText = message;
                    showToast(message, true);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    const message = data.message || 'Checkout failed.';
                    if (el) el.innerText = message;
                    showToast(message, false);
                }
            }).catch(err => {
                const message = 'Checkout failed. Check connection or server.';
                const el = document.getElementById('checkoutMessage');
                if (el) el.innerText = message;
                showToast(message, false);
            });
        }

        // RECREATED: View User Modal
        window.viewUser = function(userId) {
            try {
                console.log('Opening view modal for user:', userId);
                
                const url = '{{ route('management.users.show', 'ID') }}'.replace('ID', userId);
                console.log('Fetching from URL:', url);
                
                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.json();
                    })
                    .then(user => {
                        console.log('User data received:', user);
                        
                        // Populate modal fields
                        document.getElementById('viewUserName').textContent = user.name || 'N/A';
                        document.getElementById('viewUserEmail').textContent = user.email || 'N/A';
                        
                        const roleNames = (user.roles && user.roles.length > 0)
                            ? user.roles.map(r => r.name).join(', ')
                            : 'No roles assigned';
                        document.getElementById('viewUserRoles').textContent = roleNames;
                        
                        // Show modal
                        const modal = document.getElementById('viewUserModal');
                        modal.style.display = 'flex';
                        console.log('Modal displayed');
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        alert('Failed to load user. Check console for details.');
                    });
            } catch (e) {
                console.error('Exception in viewUser:', e);
                alert('Error: ' + e.message);
            }
        };

        // RECREATED: Edit User Modal
        window.editUser = function(userId) {
            try {
                console.log('Opening edit modal for user:', userId);
                
                const url = '{{ route('management.users.show', 'ID') }}'.replace('ID', userId);
                console.log('Fetching from URL:', url);
                
                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.json();
                    })
                    .then(user => {
                        console.log('User data received:', user);
                        
                        // Pre-fill form
                        document.getElementById('editUserId').value = userId;
                        document.getElementById('editUserName').value = user.name || '';
                        document.getElementById('editUserEmail').value = user.email || '';
                        
                        // Show modal
                        const modal = document.getElementById('editUserModal');
                        modal.style.display = 'flex';
                        console.log('Edit modal displayed');
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        alert('Failed to load user. Check console for details.');
                    });
            } catch (e) {
                console.error('Exception in editUser:', e);
                alert('Error: ' + e.message);
            }
        };

        // RECREATED: Submit Edit User
        window.submitEditUser = function() {
            try {
                const userId = document.getElementById('editUserId').value;
                const name = document.getElementById('editUserName').value;
                const email = document.getElementById('editUserEmail').value;
                
                console.log('Submitting edit for user:', userId, { name, email });
                
                if (!name || !email) {
                    alert('Name and email are required');
                    return;
                }
                
                const url = '{{ route('management.users.update', 'ID') }}'.replace('ID', userId);
                
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        ...csrfHeaders(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, email })
                })
                .then(response => {
                    console.log('Update response status:', response.status);
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Update successful:', data);
                    alert('User updated successfully');
                    document.getElementById('editUserModal').style.display = 'none';
                    setTimeout(() => location.reload(), 500);
                })
                .catch(error => {
                    console.error('Update error:', error);
                    alert('Failed to update user. Check console for details.');
                });
            } catch (e) {
                console.error('Exception in submitEditUser:', e);
                alert('Error: ' + e.message);
            }
        };

        function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;
            fetch('{{ route('management.users.destroy', ':id') }}'.replace(':id', id), { 
                method: 'DELETE', 
                headers: csrfHeaders() 
            }).then(r => {
                if (r.ok) { 
                    alert('User deleted successfully'); 
                    location.reload(); 
                } else {
                    return r.json().then(data => {
                        alert('Failed to delete user: ' + (data.message || 'Unknown error'));
                    });
                }
            }).catch(e => alert('Error: ' + e));
        }

        function submitAddUser() {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            fetch('{{ route('management.users.store') }}', {
                method: 'POST',
                headers: csrfHeaders(),
                body: JSON.stringify(data)
            }).then(r => {
                if (r.ok) { alert('User added successfully'); location.reload(); }
                else { alert('Failed to add user'); }
            }).catch(e => alert('Error: ' + e));
        }

        function submitAddRole() {
            const form = document.getElementById('addRoleForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            fetch('{{ route('management.roles.store') }}', {
                method: 'POST',
                headers: csrfHeaders(),
                body: JSON.stringify(data)
            }).then(r => {
                if (r.ok) { alert('Role added successfully'); location.reload(); }
                else { alert('Failed to add role'); }
            }).catch(e => alert('Error: ' + e));
        }

        function deleteRole(id) {
            if (!confirm('Are you sure?')) return;
            fetch('{{ route('management.roles.destroy', ':id') }}'.replace(':id', id), { 
                method: 'DELETE', 
                headers: csrfHeaders() 
            }).then(r => {
                if (r.ok) { alert('Role deleted'); location.reload(); }
                else { alert('Failed to delete role'); }
            }).catch(e => alert('Error: ' + e));
        }
    </script>
</html>