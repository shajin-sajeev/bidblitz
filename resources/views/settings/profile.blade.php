@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 600px; margin: 2rem auto;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1>Profile Settings</h1>
    </div>

    <!-- Current Profile Photo -->
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="position: relative; display: inline-block;">
            @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Photo" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary);">
            @else
                <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid var(--primary);">
                    <svg width="48" height="48" fill="white" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            @endif
        </div>
        <p style="margin-top: 1rem; color: var(--text-muted);">Your current profile photo</p>
    </div>

    <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf
        
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" class="form-control" value="{{ $user->username }}" readonly>
            <small style="color: var(--text-muted);">Username is unique and cannot be changed</small>
        </div>

        <div class="form-group">
            <label for="specialization" class="form-label">Specialization</label>
            <select name="specialization" id="specialization" class="form-control" required>
                <option value="">Select Specialization</option>
                <option value="Batsman" {{ old('specialization', $user->playerProfile->specialization ?? '') == 'Batsman' ? 'selected' : '' }}>Batsman</option>
                <option value="Bowler" {{ old('specialization', $user->playerProfile->specialization ?? '') == 'Bowler' ? 'selected' : '' }}>Bowler</option>
                <option value="All-rounder" {{ old('specialization', $user->playerProfile->specialization ?? '') == 'All-rounder' ? 'selected' : '' }}>All-rounder</option>
                <option value="Wicket-keeper" {{ old('specialization', $user->playerProfile->specialization ?? '') == 'Wicket-keeper' ? 'selected' : '' }}>Wicket-keeper</option>
            </select>
        </div>

        <div class="form-group">
            <label for="skills" class="form-label">Player Skills</label>
            <textarea name="skills" id="skills" class="form-control" rows="4" placeholder="Enter your player skills (e.g., Fast bowling, Power hitting, Fielding at covers...)">{{ old('skills', $user->playerProfile->description ?? '') }}</textarea>
            <small style="color: var(--text-muted);">Describe your key skills and strengths as a player</small>
        </div>

        <div class="form-group">
            <label for="profile_image" class="form-label">Profile Photo</label>
            <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
            <small style="color: var(--text-muted);">Allowed formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
        </div>

        <!-- Image Preview -->
        <div id="image-preview" style="margin-top: 1rem; display: none;">
            <p style="margin-bottom: 0.5rem; color: var(--text-muted);">Preview:</p>
            <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid var(--border);">
        </div>

        <div class="mt-8">
            <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
        </div>
    </form>
</div>

<script>
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
@endsection
