@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card">
            <h1>Settings & Profile</h1>
            <p>Manage your account settings, password, and personal information.</p>
        </section>

        @if(session('success'))
            <section class="glass-card" style="background: rgba(34, 197, 94, 0.2); border-left: 4px solid rgba(34, 197, 94, 0.6);">
                <p style="margin: 0; color: #22c55e; font-weight: 600;">✓ {{ session('success') }}</p>
            </section>
        @endif

        @if(session('error'))
            <section class="glass-card" style="background: rgba(239, 68, 68, 0.2); border-left: 4px solid rgba(239, 68, 68, 0.6);">
                <p style="margin: 0; color: #ef4444; font-weight: 600;">✗ {{ session('error') }}</p>
            </section>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 360px; gap: 20px;">
            <div>
                <!-- Profile Information -->
                <section class="glass-card">
                    <h2 style="margin-top: 0; color: #dc2626;">Profile Information</h2>
                    <form id="profileForm" method="POST" action="{{ route('settings.updateProfile') }}" style="display: flex; flex-direction: column; gap: 16px;">
                        @csrf

                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="glass-input" placeholder="Your full name" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                            @error('name')
                                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="glass-input" placeholder="your@email.com" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                            @error('email')
                                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Phone (Optional)</label>
                            <input type="tel" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" class="glass-input" placeholder="+62 8xx xxxx xxxx" style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                        </div>

                        <button type="submit" class="btn" style="background: #22c55e; color: white; margin-top: 8px;">Save Profile Changes</button>
                        <p id="profileMessage" style="margin-top: 10px; color: #f8fafc; font-size: 14px;"></p>
                    </form>
                </section>

                <!-- Change Password -->
                <section class="glass-card" style="margin-top: 20px;">
                    <h2 style="margin-top: 0; color: #dc2626;">Change Password</h2>
                    <form id="passwordForm" method="POST" action="{{ route('settings.updatePassword') }}" style="display: flex; flex-direction: column; gap: 16px;">
                        @csrf

                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Current Password</label>
                            <div style="position: relative;">
                                <input type="password" name="current_password" id="currentPassword" class="glass-input" placeholder="••••••••" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                                <button type="button" onclick="togglePasswordVisibility('currentPassword')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 18px;">👁️</button>
                            </div>
                            @error('current_password')
                                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">New Password</label>
                            <div style="position: relative;">
                                <input type="password" name="new_password" id="newPassword" class="glass-input" placeholder="••••••••" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                                <button type="button" onclick="togglePasswordVisibility('newPassword')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 18px;">👁️</button>
                            </div>
                            @error('new_password')
                                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Confirm New Password</label>
                            <div style="position: relative;">
                                <input type="password" name="new_password_confirmation" id="confirmPassword" class="glass-input" placeholder="••••••••" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                                <button type="button" onclick="togglePasswordVisibility('confirmPassword')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 18px;">👁️</button>
                            </div>
                        </div>

                        <button type="submit" class="btn" style="background: #f59e0b; color: white; margin-top: 8px;">Update Password</button>
                        <p id="passwordMessage" style="margin-top: 10px; color: #f8fafc; font-size: 14px;"></p>
                    </form>
                </section>
            </div>

            <!-- Account Summary Sidebar -->
            <div>
                <section class="glass-card">
                    <h3 style="margin-top: 0; color: #dc2626;">Account Summary</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 4px;">ACCOUNT HOLDER</div>
                            <div style="font-size: 16px; font-weight: 700; color: #fde68a;">{{ Auth::user()->name }}</div>
                        </div>

                        <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 12px;">
                            <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 4px;">EMAIL</div>
                            <div style="font-size: 13px; color: #e5e7eb;">{{ Auth::user()->email }}</div>
                        </div>

                        @if(Auth::user()->phone)
                        <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 12px;">
                            <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 4px;">PHONE</div>
                            <div style="font-size: 13px; color: #e5e7eb;">{{ Auth::user()->phone }}</div>
                        </div>
                        @endif

                        <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 12px;">
                            <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 4px;">MEMBER SINCE</div>
                            <div style="font-size: 13px; color: #e5e7eb;">{{ Auth::user()->created_at->format('d M Y') }}</div>
                        </div>

                        <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 12px;">
                            <div style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 4px;">ROLES</div>
                            <div style="font-size: 13px; color: #e5e7eb;">
                                @forelse(Auth::user()->roles as $role)
                                    <span style="display: inline-block; background: rgba(34, 197, 94, 0.35); color: #fef9c3; border-radius: 999px; padding: 4px 12px; font-size: 12px; margin-right: 4px; margin-bottom: 4px;">{{ $role->name }}</span>
                                @empty
                                    <span style="color: #ef4444;">No roles assigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-danger" style="width: 100%; margin-top: 20px; padding: 10px; font-size: 14px;" onclick="confirmLogout()">Logout</button>
                </section>
            </div>
        </div>
    </main>

    <script>
        function csrfHeaders() {
            return {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            };
        }

        function togglePasswordVisibility(id) {
            const el = document.getElementById(id);
            el.type = el.type === 'password' ? 'text' : 'password';
        }

        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('logout') }}';
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Handle profile form submission
        document.getElementById('profileForm')?.addEventListener('submit', function(e) {
            // Allow normal form submission for regular requests
        });

        // Handle password form submission
        document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
            // Allow normal form submission for regular requests
        });
    </script>

@endsection
