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
    <div class="profile-stats-grid mb-8">
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
</div>

<style>
.profile-container {
    max-width: 1180px;
    margin: 0 auto;
    padding: 1rem;
    display: grid;
    gap: 1.5rem;
}

.profile-header-card {
    position: relative;
    overflow: hidden;
    padding: 2rem;
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background:
        linear-gradient(135deg, rgba(251, 191, 36, 0.16), rgba(14, 165, 233, 0.08) 48%, rgba(15, 23, 42, 0.78)),
        var(--card-bg);
    box-shadow: 0 20px 55px rgba(0, 0, 0, 0.24);
}

.profile-header-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-top: 4px solid var(--primary);
    pointer-events: none;
}

.profile-header-content {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    align-items: center;
    gap: 2rem;
}

.profile-avatar-medium {
    position: relative;
    display: grid;
    justify-items: center;
    gap: 1rem;
}

.profile-avatar-img {
    width: 132px;
    height: 132px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(251, 191, 36, 0.95);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.28), 0 0 0 8px rgba(251, 191, 36, 0.12);
    transition: all 0.3s ease;
}

.profile-avatar-img:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 42px rgba(0, 0, 0, 0.32), 0 0 0 9px rgba(251, 191, 36, 0.16);
}

.profile-avatar-default {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.2rem;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.35), rgba(14, 165, 233, 0.2));
}

.profile-actions-overlay {
    display: flex;
    gap: 0.7rem;
    flex-wrap: wrap;
    justify-content: center;
    width: min(100%, 330px);
}

.btn-edit-profile,
.btn-upload-photo {
    min-height: 42px;
    padding: 0.75rem 1rem;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 800;
    line-height: 1;
    transition: all 0.3s ease;
    gap: 0.5rem;
    cursor: pointer;
    border: 1px solid transparent;
    white-space: nowrap;
}

.btn-edit-profile {
    background: var(--primary);
    color: #111827;
    box-shadow: 0 10px 24px rgba(251, 191, 36, 0.22);
}

.btn-edit-profile:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(251, 191, 36, 0.3);
}

.btn-upload-photo {
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-main);
    border-color: rgba(255, 255, 255, 0.16);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.16);
}

.btn-upload-photo:hover {
    background: rgba(255, 255, 255, 0.16);
    transform: translateY(-2px);
}

.profile-info-main {
    min-width: 0;
}

.profile-name-main {
    margin: 0;
    font-size: clamp(2rem, 4vw, 3.5rem);
    line-height: 1;
    font-weight: 900;
    color: var(--text-main);
    overflow-wrap: anywhere;
}

.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.2rem;
}

.profile-role-main,
.profile-contact,
.profile-member-since {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.6rem 0.85rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: var(--text-main);
    font-weight: 700;
    font-size: 0.92rem;
    overflow-wrap: anywhere;
}

.profile-role-main {
    background: rgba(251, 191, 36, 0.16);
    color: var(--primary);
    border-color: rgba(251, 191, 36, 0.22);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.profile-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}

.stat-card-enhanced {
    position: relative;
    overflow: hidden;
    min-height: 160px;
    padding: 1.35rem;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.09), rgba(255, 255, 255, 0.03)),
        var(--card-bg);
    display: grid;
    align-content: center;
    gap: 0.45rem;
    transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
}

.stat-card-enhanced::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), rgba(14, 165, 233, 0.8));
}

.stat-card-enhanced:hover {
    transform: translateY(-4px);
    border-color: rgba(251, 191, 36, 0.28);
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
}

.stat-icon-enhanced {
    width: 48px;
    height: 48px;
    margin: 0 auto 0.35rem;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: rgba(251, 191, 36, 0.14);
    font-size: 1.7rem;
}

.stat-value-enhanced {
    font-size: clamp(2rem, 4vw, 2.9rem);
    font-weight: 900;
    color: var(--primary);
    line-height: 1;
}

.stat-label-enhanced {
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
@media (max-width: 768px) {
    .profile-container {
        width: 100%;
        padding: 0.75rem 0.65rem 1.25rem;
        gap: 1rem;
    }

    .profile-header-card {
        width: 100%;
        padding: 1.1rem;
        border-radius: 22px;
        margin-bottom: 0 !important;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.22);
    }

    .profile-header-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 1rem;
        justify-items: center;
    }

    .profile-avatar-medium {
        width: 100%;
        gap: 0.9rem;
    }

    .profile-avatar-img,
    .profile-avatar-default {
        width: 102px;
        height: 102px;
        font-size: 2.45rem;
        border-width: 3px;
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.24), 0 0 0 6px rgba(251, 191, 36, 0.12);
    }

    .profile-info-main {
        width: 100%;
        text-align: center;
    }

    .profile-name-main {
        font-size: clamp(1.65rem, 8vw, 2.15rem);
        line-height: 1.08;
    }

    .profile-meta {
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.9rem;
        width: 100%;
    }

    .profile-role-main,
    .profile-contact,
    .profile-member-since {
        width: 100%;
        justify-content: center;
        min-height: 42px;
        padding: 0.68rem 0.75rem;
        border-radius: 16px;
        font-size: 0.84rem;
        line-height: 1.25;
    }

    .profile-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 0 !important;
    }

    .stat-card-enhanced {
        min-height: 128px;
        padding: 0.95rem 0.7rem;
        border-radius: 18px;
    }

    .stat-card-enhanced:hover {
        transform: none;
    }

    .stat-value-enhanced {
        font-size: 1.95rem;
    }

    .stat-icon-enhanced {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        font-size: 1.35rem;
    }

    .stat-label-enhanced {
        font-size: 0.72rem;
        line-height: 1.25;
    }

    .profile-actions-overlay {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.6rem;
        align-items: stretch;
    }

    .btn-edit-profile,
    .btn-upload-photo {
        min-width: 0;
        min-height: 46px;
        padding: 0.75rem 0.5rem;
        border-radius: 15px;
        font-size: 0.78rem;
        white-space: normal;
    }
}

@media (max-width: 430px) {
    .profile-stats-grid {
        grid-template-columns: 1fr;
    }

    .profile-actions-overlay {
        grid-template-columns: 1fr;
    }

    .stat-card-enhanced {
        min-height: 118px;
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


