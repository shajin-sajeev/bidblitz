<!-- User Profile Card -->
<div class="glass-card profile-card mb-4">
    <div class="profile-header">
        @if(auth()->user()->profile_image)
            <div class="profile-avatar-container">
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="profile-avatar">
                <label for="quick-photo-upload" class="upload-overlay">
                    <span class="upload-icon">📷</span>
                </label>
            </div>
        @else
            <div class="profile-avatar-container">
                <div class="profile-avatar profile-avatar-default">
                    👤
                </div>
                <label for="quick-photo-upload" class="upload-overlay">
                    <span class="upload-icon">📷</span>
                </label>
            </div>
        @endif
    </div>
    
    <!-- Hidden file input for quick upload -->
    <input type="file" id="quick-photo-upload" style="display: none;" accept="image/*">
    
    <div class="profile-info">
        <h3 class="profile-name">{{ auth()->user()->name ?? 'User ' . auth()->user()->phone }}</h3>
        <div class="profile-role">{{ auth()->user()->playerProfile->player_role ?? 'Player' }}</div>
        <div class="profile-stats">
            <div class="stat-item">
                <span class="stat-value">{{ \App\Models\Team::where('owner_id', auth()->id())->count() }}</span>
                <span class="stat-label">Teams</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ \App\Models\Auction::where('created_by', auth()->id())->count() }}</span>
                <span class="stat-label">Auctions</span>
            </div>
        </div>
    </div>
    
    <div class="profile-actions">
        <a href="{{ route('profile.show') }}" class="btn btn-primary btn-sm">
            👤 View Profile
        </a>
    </div>
</div>

<!-- Navigation Menu -->
<div class="glass-card">
    <h5 style="margin-bottom: 1rem; color: var(--primary);">🏠 Dashboard</h5>
    
    <div style="margin-bottom: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Auctions</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            📊 Overview
        </a>
        <a href="{{ route('auctions.create') }}" class="nav-item {{ request()->routeIs('auctions.create') ? 'active' : '' }}">
            ➕ Create Auction
        </a>
        <a href="{{ route('auctions.join') }}" class="nav-item {{ request()->routeIs('auctions.join*') ? 'active' : '' }}">
            🔗 Join Auction
        </a>
        <a href="{{ route('auctions.joined') }}" class="nav-item {{ request()->routeIs('auctions.joined') ? 'active' : '' }}">
            📋 Joined Auctions
        </a>
        <a href="{{ route('auctions.history') }}" class="nav-item {{ request()->routeIs('auctions.history') ? 'active' : '' }}">
            📜 Auction History
        </a>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Teams</div>
        <a href="{{ route('teams.joined') }}" class="nav-item {{ request()->routeIs('teams.*') ? 'active' : '' }}">
            👥 Joined Teams
        </a>
    </div>

    </div>

<style>
.nav-item {
    display: block;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    color: var(--text-main);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.nav-item:hover {
    background: rgba(251, 191, 36, 0.1);
    color: var(--primary);
}

.nav-item.active {
    background: rgba(251, 191, 36, 0.2);
    color: var(--primary);
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quickUpload = document.getElementById('quick-photo-upload');
    if (quickUpload) {
        quickUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    alert('File size must be less than 2MB.');
                    return;
                }
                
                // Create FormData for upload
                const formData = new FormData();
                formData.append('profile_image', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                
                // Upload via fetch
                fetch('{{ route("settings.profile.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show new photo
                        window.location.reload();
                    } else {
                        alert(data.message || 'Upload failed. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Upload failed. Please try again.');
                });
            }
        });
    }
});
</script>
