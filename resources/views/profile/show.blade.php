@extends('layouts.app')

@section('content')
<div class="profile-container">
    <!-- Profile Header -->
    <div class="glass-card profile-header-card mb-8">
        <div class="profile-header-content">
            <div class="profile-avatar-medium">
                @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="profile-avatar-img">
                @else
                    <div class="profile-avatar-img profile-avatar-default">
                        👤
                    </div>
                @endif
                <div class="profile-actions-overlay">
                    <a href="{{ route('settings.profile') }}" class="btn-edit-profile">
                        ⚙️ <span>Edit Profile</span>
                    </a>
                    <form id="profile-photo-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                    </form>
                    
                    <label for="profile-photo-upload" class="btn-upload-photo">
                        📸 <span>Change Photo</span>
                    </label>
                    <input type="file" id="profile-photo-upload" style="display: none;" accept="image/*">
                </div>
            </div>
            
            <div class="profile-info-main">
                <h1 class="profile-name-main">
                    {{ $user->name }}
                </h1>
                <div class="profile-meta">
                    <div class="profile-role-main">
                        {{ $user->playerProfile->player_role ?? 'Player' }}
                    </div>
                    <div class="profile-contact">
                        @if($user->email)
                            📧 {{ $user->email }}
                        @else
                            📱 {{ $user->phone }}
                        @endif
                    </div>
                    <div class="profile-member-since">
                        🆔 Member since {{ $user->created_at->format('M j, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="glass-card text-center stat-card-enhanced">
            <div class="stat-icon-enhanced">🏆</div>
            <div class="stat-value-enhanced">{{ $stats['teams_owned'] }}</div>
            <div class="stat-label-enhanced">Teams Owned</div>
        </div>
        <div class="glass-card text-center stat-card-enhanced">
            <div class="stat-icon-enhanced">🎯</div>
            <div class="stat-value-enhanced">{{ $stats['auctions_created'] }}</div>
            <div class="stat-label-enhanced">Auctions Created</div>
        </div>
        <div class="glass-card text-center stat-card-enhanced">
            <div class="stat-icon-enhanced">👥</div>
            <div class="stat-value-enhanced">{{ $stats['teams_joined'] }}</div>
            <div class="stat-label-enhanced">Teams Joined</div>
        </div>
        <div class="glass-card text-center stat-card-enhanced">
            <div class="stat-icon-enhanced">⚡</div>
            <div class="stat-value-enhanced">{{ $stats['total_players'] }}</div>
            <div class="stat-label-enhanced">Players Acquired</div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="glass-card activity-card-enhanced">
        <h3 class="activity-title-enhanced">📈 Recent Activity</h3>
        @php
            $recentActivity = \App\Models\AuctionHistory::with(['auction', 'bidder'])
                ->where('bidder_id', $user->id)
                ->orderBy('action_at', 'desc')
                ->limit(10)
                ->get();
        @endphp
        @forelse($recentActivity as $activity)
            <div class="activity-item-enhanced">
                <div class="activity-content">
                    <div class="activity-header-enhanced">
                        <div class="activity-action">
                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                            @if($activity->auction) in {{ $activity->auction->name }} @endif
                        </div>
                        <div class="activity-time-enhanced">
                            {{ $activity->action_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($activity->amount)
                        <div class="activity-amount-enhanced">
                            ${{ number_format($activity->amount, 2) }}
                        </div>
                    @endif
                    @if($activity->bidder)
                        <div class="activity-user">
                            by {{ $activity->bidder->name ?? 'Unknown' }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state-enhanced">
                <div class="empty-icon">📊</div>
                <h4>No Recent Activity</h4>
                <p>Your auction activity will appear here once you start participating in auctions.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.profile-header-card {
    padding: 2rem;
    position: relative;
    overflow: hidden;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.activity-item {
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: rgba(251, 191, 36, 0.05);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.activity-title {
    font-weight: 600;
    color: var(--text-main);
}

.activity-time {
    color: var(--text-muted);
    font-size: 0.85rem;
}

.activity-amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary);
}

.profile-avatar-medium {
    position: relative;
}

.profile-avatar-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary);
    box-shadow: 0 6px 24px rgba(251, 191, 36, 0.3);
    transition: all 0.3s ease;
}

.profile-avatar-img:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 28px rgba(251, 191, 36, 0.4);
}

.profile-avatar-default {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.2));
    border: 3px solid var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    box-shadow: 0 6px 24px rgba(251, 191, 36, 0.3);
    transition: all 0.3s ease;
}

.profile-avatar-default:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 28px rgba(251, 191, 36, 0.4);
}

.profile-actions-overlay {
    position: absolute;
    bottom: 8px;
    right: 8px;
    display: flex;
    gap: 0.75rem;
}

.btn-edit-profile {
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    background: var(--primary);
    color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    gap: 0.5rem;
}

.btn-edit-profile:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(251, 191, 36, 0.4);
}

.btn-edit-profile span {
    white-space: nowrap;
}

.btn-upload-photo {
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    background: var(--accent);
    color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    gap: 0.5rem;
}

.btn-upload-photo:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(251, 191, 36, 0.4);
}

.btn-upload-photo span {
    white-space: nowrap;
}

@media (max-width: 768px) {
    .profile-header-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .profile-info-main {
        text-align: center;
    }
    
    .profile-meta {
        align-items: center;
        gap: 0.5rem;
    }
    
    .grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-value-enhanced {
        font-size: 2.5rem;
    }
    
    .stat-icon-enhanced {
        font-size: 2rem;
    }
    
    .profile-actions-overlay {
        position: static;
        margin-top: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .btn-edit-profile,
    .btn-upload-photo {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoUpload = document.getElementById('profile-photo-upload');
    if (photoUpload) {
        photoUpload.addEventListener('change', function(e) {
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
                
                // Get form data
                const form = document.getElementById('profile-photo-form');
                const formData = new FormData(form);
                formData.append('profile_image', file);
                
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
@endsection
