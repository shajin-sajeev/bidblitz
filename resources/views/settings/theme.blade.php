@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 600px; margin: 2rem auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1>App Theme</h1>
        <a href="{{ route('settings.index') }}" class="btn btn-outline">← Back to Settings</a>
    </div>

    <form action="{{ route('settings.theme.update') }}" method="POST" class="theme-form">
        @csrf
        
        <div class="theme-options">
            <h3 style="margin-bottom: 1.5rem;">Choose Your Theme</h3>
            
            <div class="theme-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <!-- Dark Theme Option -->
                <div class="theme-option {{ session('theme') === 'dark' || !session('theme') ? 'selected' : '' }}" 
                     onclick="selectTheme('dark')">
                    <div class="theme-preview dark-preview" style="background: #1a1a1a; border: 2px solid var(--border); border-radius: 12px; padding: 1rem; height: 120px; position: relative;">
                        <div style="background: #2d2d2d; border-radius: 4px; height: 20px; margin-bottom: 0.5rem;"></div>
                        <div style="background: #404040; border-radius: 4px; height: 12px; margin-bottom: 0.5rem; width: 80%;"></div>
                        <div style="background: #404040; border-radius: 4px; height: 12px; width: 60%;"></div>
                        <div style="position: absolute; top: 0.5rem; right: 0.5rem; width: 16px; height: 16px; border-radius: 50%; background: #666;"></div>
                    </div>
                    <div style="text-align: center; margin-top: 0.75rem;">
                        <h4 style="margin: 0; color: var(--text-primary);">Dark Theme</h4>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.85rem;">Default dark mode</p>
                    </div>
                    <input type="radio" name="theme" value="dark" {{ session('theme') === 'dark' || !session('theme') ? 'checked' : '' }} style="display: none;">
                </div>

                <!-- Light Theme Option -->
                <div class="theme-option {{ session('theme') === 'light' ? 'selected' : '' }}" 
                     onclick="selectTheme('light')">
                    <div class="theme-preview light-preview" style="background: #ffffff; border: 2px solid var(--border); border-radius: 12px; padding: 1rem; height: 120px; position: relative;">
                        <div style="background: #f8f9fa; border-radius: 4px; height: 20px; margin-bottom: 0.5rem;"></div>
                        <div style="background: #e9ecef; border-radius: 4px; height: 12px; margin-bottom: 0.5rem; width: 80%;"></div>
                        <div style="background: #e9ecef; border-radius: 4px; height: 12px; width: 60%;"></div>
                        <div style="position: absolute; top: 0.5rem; right: 0.5rem; width: 16px; height: 16px; border-radius: 50%; background: #ffd700;"></div>
                    </div>
                    <div style="text-align: center; margin-top: 0.75rem;">
                        <h4 style="margin: 0; color: var(--text-primary);">Light Theme</h4>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.85rem;">Bright and clean</p>
                    </div>
                    <input type="radio" name="theme" value="light" {{ session('theme') === 'light' ? 'checked' : '' }} style="display: none;">
                </div>
            </div>
        </div>

        <div class="mt-8">
            <button type="submit" class="btn btn-primary btn-block">Apply Theme</button>
        </div>
    </form>
</div>

<style>
.theme-option {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 12px;
    padding: 0.5rem;
}

.theme-option:hover {
    transform: translateY(-2px);
}

.theme-option.selected {
    background: var(--primary);
    color: white;
}

.theme-option.selected .theme-preview {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.theme-option.selected h4,
.theme-option.selected p {
    color: white;
}

.light-preview {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dark-preview {
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
</style>

<script>
function selectTheme(theme) {
    // Remove selected class from all options
    document.querySelectorAll('.theme-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');
    
    // Check the corresponding radio button
    document.querySelector(`input[name="theme"][value="${theme}"]`).checked = true;
}

// Apply theme immediately when changed for better UX
document.querySelectorAll('input[name="theme"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'light') {
            document.body.classList.add('light-theme');
            document.body.classList.remove('dark-theme');
        } else {
            document.body.classList.add('dark-theme');
            document.body.classList.remove('light-theme');
        }
    });
});
</script>
@endsection
