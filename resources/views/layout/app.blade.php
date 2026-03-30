<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cashier POS Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, rgba(255,255,255,0.26), rgba(253, 224, 71, 0.28)), radial-gradient(circle at 20% 20%, #fed7aa 0%, #fdba74 45%, #dc2626 100%); color: #1f2937; min-height: 100vh; }

        .app-container { min-height: 100vh; }

        .navbar { position: fixed; top: 0; left: 0; right: 0; height: 72px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; background: rgba(220, 38, 38, 0.85); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.25); z-index: 4000; }
        .navbar .brand { font-size: 24px; font-weight: 800; color: #fef3c7; letter-spacing: 1px; }
        .navbar nav { display: flex; align-items: center; gap: 10px; }
        .navbar nav a { color: #fef3c7; text-decoration: none; padding: 8px 12px; border-radius: 10px; font-weight: 600; transition: all 0.2s ease; }
        .navbar nav a.active, .navbar nav a:hover { background: rgba(248, 113, 113, 0.35); }
        .navbar .cta { display: flex; align-items: center; gap: 10px; }

        .main-content { margin: 92px 20px 20px 20px; padding: 16px; }


        .glass-card { background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 20px; box-shadow: 0 10px 28px rgba(127, 29, 29, 0.35); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 22px; margin-bottom: 20px; transition: transform 0.2s, box-shadow 0.2s; }
        .glass-card:hover { transform: translateY(-3px); box-shadow: 0 14px 34px rgba(127, 29, 29, 0.45); }

        .modal-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 3000; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 22px; color: #1f2937; }
        .modal-close { position: absolute; right: 14px; top: 12px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700; }

        .glass-input, .glass-select, .glass-textarea { width: 100%; height: 54px; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; margin-top: 8px; font-size: 16px; font-weight: 500; }
        .glass-input::placeholder, .glass-select option, .glass-textarea::placeholder { color: #6b7280; }
        .glass-input:focus, .glass-select:focus, .glass-textarea:focus { border-color: #dc2626; background: rgba(255, 255, 255, 0.95); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25); }

        .table-wrap table th, .table-wrap table td { color: #1f2937; }

        .btn { border: none; color: #ffffff; font-weight: 700; border-radius: 14px; cursor: pointer; padding: 12px 20px; background: linear-gradient(155deg, rgba(220, 38, 38, 0.95), rgba(239, 68, 68, 0.9)); box-shadow: 0 8px 18px rgba(185, 28, 28, 0.5); transition: transform 0.2s, filter 0.2s; }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
        .btn-secondary { background: linear-gradient(155deg, rgba(245, 158, 11, 0.9), rgba(251, 191, 36, 0.85)); }
        .btn-danger { background: linear-gradient(155deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.85)); }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; table-layout: auto; background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(253, 224, 71, 0.75); border-radius: 16px; }
        th, td { padding: 12px 14px; color: #1f2937; border-bottom: 1px solid rgba(252, 211, 77, 0.35); }
        th { font-weight: 700; text-align: left; background: rgba(254, 249, 195, 0.85); color: #92400e; }
        tr:nth-child(even) { background: rgba(254, 240, 138, 0.57); }
        tr:hover { background: rgba(253, 230, 138, 0.8); }

        /* Login Modal Styles */
        .login-modal .modal-content { max-width: 400px; width: 100%; position: relative; }
        .login-modal .modal-content h3 { margin: 0 0 20px; font-size: 24px; color: #1f2937; text-align: center; }
        .login-modal .form-group { margin-bottom: 16px; }
        .login-modal .form-group label { display: block; margin-bottom: 4px; font-weight: 600; color: #374151; }
        .login-modal .error-message { color: #dc2626; font-size: 14px; margin-top: 4px; }
        .login-modal .success-message { color: #059669; font-size: 14px; margin-top: 4px; }

    </style>
</head>
<body>
    <div class="app-container">
        @yield('sidebar')

        <!-- Login Modal -->
        <div id="loginModal" class="modal-overlay login-modal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeLoginModal()">&times;</span>
                <h3>Login to Your Account</h3>

                <div id="loginMessage"></div>

                <form id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label for="loginEmail">Email Address</label>
                        <input type="email" id="loginEmail" name="email" class="glass-input" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="loginPassword" name="password" class="glass-input" placeholder="Enter your password" required style="padding-right: 42px;">
                            <button type="button" id="loginPasswordToggle" onclick="togglePasswordVisibility('loginPassword', 'loginPasswordToggle')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: #facc15; font-size: 16px;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" name="remember" style="margin-right: 8px;">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn" style="width: 100%;">Sign In</button>
                </form>
            </div>
        </div>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('active');
            document.getElementById('loginEmail').focus();
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('active');
            document.getElementById('loginForm').reset();
            document.getElementById('loginMessage').innerHTML = '';
        }

        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        // Toggle password visibility
        function togglePasswordVisibility(fieldId, toggleBtnId) {
            const input = document.getElementById(fieldId);
            const toggle = document.getElementById(toggleBtnId);
            if (!input || !toggle) return;

            const icon = toggle.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        }

        // Handle login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const messageDiv = document.getElementById('loginMessage');

            fetch('/login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<div class="success-message">' + data.message + '</div>';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    messageDiv.innerHTML = '<div class="error-message">' + data.message + '</div>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<div class="error-message">An error occurred. Please try again.</div>';
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
