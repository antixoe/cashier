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
                <p style="margin: 0; color: #22c55e; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</p>
            </section>
        @endif

        @if(session('error'))
            <section class="glass-card" style="background: rgba(239, 68, 68, 0.2); border-left: 4px solid rgba(239, 68, 68, 0.6);">
                <p style="margin: 0; color: #ef4444; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</p>
            </section>
        @endif

        <!-- Settings Navbar/Tabs -->
        <div style="display: flex; gap: 0; margin-bottom: 20px; border-bottom: 2px solid rgba(255,255,255,0.2);">
            <button type="button" onclick="switchTab('profile')" id="tab-profile" class="settings-tab active" style="padding: 12px 24px; border: none; background: rgba(253, 230, 138, 0.15); color: #f5f5f5; font-weight: 600; cursor: pointer; border-bottom: 3px solid #fde68a; margin-bottom: -2px; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-file-earmark-text" style="font-size: 18px;"></i> Profile Settings
            </button>
            <button type="button" onclick="switchTab('activity')" id="tab-activity" class="settings-tab" style="padding: 12px 24px; border: none; background: rgba(255,255,255,0.05); color: #d1d5db; font-weight: 600; cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-graph-up" style="font-size: 18px;"></i> Activity Logs
            </button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 360px; gap: 20px;">
            <div>
                <!-- Profile Settings Tab -->
                <div id="profile-tab" style="display: block;">
                    <!-- Profile Information -->
                    <section class="glass-card">
                    <h2 style="margin-top: 0; color: #dc2626;">Profile Information</h2>
                    <form id="profileForm" method="POST" action="{{ route('settings.updateProfile') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 16px;">
                        @csrf

                        <!-- Profile Image -->
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Profile Picture</label>
                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                <div style="flex-shrink: 0;">
                                    @if(Auth::user()->profile_image)
                                        <img id="profileImagePreview" src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile" style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(220, 38, 38, 0.5);">
                                    @else
                                        <div id="profileImagePreview" style="width: 120px; height: 120px; border-radius: 12px; background: rgba(220, 38, 38, 0.2); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(220, 38, 38, 0.5);">
                                            <i class="bi bi-person-circle" style="font-size: 48px; color: #dc2626;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="profile_image" id="profileImageInput" accept="image/*" style="display: none;">
                                    <button type="button" onclick="document.getElementById('profileImageInput').click()" class="btn" style="background: #3b82f6; color: white; margin-bottom: 8px;">
                                        <i class="bi bi-upload"></i> Change Picture
                                    </button>
                                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">JPG, PNG, GIF or WebP format. Max 5MB.</p>
                                    @if(Auth::user()->profile_image)
                                        <button type="button" onclick="removeProfileImage()" class="btn btn-danger" style="margin-top: 8px; padding: 6px 12px; font-size: 12px;">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @error('profile_image')
                                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

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
                </div>

                <!-- Activity Logs Tab -->
                <div id="activity-tab" style="display: none;">
                    <!-- Activity Logs -->
                    <section class="glass-card" style="margin-top: 0;">
                    <h2 style="margin-top: 0; color: #dc2626;">
                        @if(auth()->user()->hasRole('admin'))
                            <i class="bi bi-graph-up"></i> All User Activities
                        @else
                            <i class="bi bi-graph-up"></i> Your Activity Logs
                        @endif
                    </h2>
                    <p style="color: #9ca3af; margin-top: 0; margin-bottom: 16px; font-size: 14px;">
                        @if(auth()->user()->hasRole('admin'))
                            View all users' activities, IP addresses, and geographic locations.
                        @else
                            View your recent actions, IP addresses, and access locations.
                        @endif
                    </p>
                    <div class="table-wrap" style="overflow-x: auto;">
                        <table style="min-width: 900px;">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    @if(auth()->user()->hasRole('admin'))
                                        <th>User</th>
                                    @endif
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                    <tr>
                                        <td style="font-size: 12px; white-space: nowrap;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        @if(auth()->user()->hasRole('admin'))
                                            <td style="font-size: 12px;">
                                                @if($log->user)
                                                    <span style="color: #fde68a;">{{ $log->user->name }}</span>
                                                @else
                                                    <span style="color: #9ca3af;">System</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <span style="display:inline-block;background:rgba(220,38,38,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:11px;font-weight:600;white-space:nowrap;">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; font-size: 12px;">
                                            {{ $log->description ?? '-' }}
                                        </td>
                                        <td style="font-size: 11px; font-family: monospace; color: #fde68a;">
                                            {{ $log->ip_address ?? 'N/A' }}
                                        </td>
                                        <td style="font-size: 12px;">
                                            @if($log->location)
                                                <span style="color: #a78bfa;">{{ $log->location['display'] }}</span>
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('admin') ? '6' : '5' }}" style="text-align:center; color:#fde68a; padding: 20px;">
                                            No activity logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($activityLogs->hasPages())
                        <div style="margin-top: 16px; display: flex; justify-content: center; gap: 8px;">
                            {{ $activityLogs->links('pagination::tailwind') }}
                        </div>
                    @endif
                </section>
                </div>
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
        // Tab switching function
        function switchTab(tab) {
            // Hide all tabs
            document.getElementById('profile-tab').style.display = 'none';
            document.getElementById('activity-tab').style.display = 'none';

            // Remove active class from all tabs
            document.getElementById('tab-profile').classList.remove('active');
            document.getElementById('tab-activity').classList.remove('active');
            
            // Reset both tabs to inactive state
            document.getElementById('tab-profile').style.color = '#d1d5db';
            document.getElementById('tab-profile').style.background = 'rgba(255,255,255,0.05)';
            document.getElementById('tab-profile').style.borderBottomColor = 'transparent';
            
            document.getElementById('tab-activity').style.color = '#d1d5db';
            document.getElementById('tab-activity').style.background = 'rgba(255,255,255,0.05)';
            document.getElementById('tab-activity').style.borderBottomColor = 'transparent';

            // Show selected tab and apply active styling
            if (tab === 'profile') {
                document.getElementById('profile-tab').style.display = 'block';
                document.getElementById('tab-profile').classList.add('active');
                document.getElementById('tab-profile').style.color = '#f5f5f5';
                document.getElementById('tab-profile').style.background = 'rgba(253, 230, 138, 0.15)';
                document.getElementById('tab-profile').style.borderBottomColor = '#fde68a';
            } else if (tab === 'activity') {
                document.getElementById('activity-tab').style.display = 'block';
                document.getElementById('tab-activity').classList.add('active');
                document.getElementById('tab-activity').style.color = '#f5f5f5';
                document.getElementById('tab-activity').style.background = 'rgba(253, 230, 138, 0.15)';
                document.getElementById('tab-activity').style.borderBottomColor = '#fde68a';
            }
        }

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

        // Profile image preview
        document.getElementById('profileImageInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('profileImagePreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = event.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'profileImagePreview';
                        img.src = event.target.result;
                        img.style.cssText = 'width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(220, 38, 38, 0.5);';
                        preview.parentElement.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        function removeProfileImage() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                const input = document.getElementById('profileImageInput');
                input.value = '';
                // You could optionally submit the form here or just clear the input
            }
        }
    </script>

@endsection
