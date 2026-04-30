@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 800px; margin: 2rem auto;">
    <h1>Settings</h1>
    <p class="text-muted">Manage your account settings and preferences</p>
    
    <div class="settings-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <a href="{{ route('settings.profile') }}" class="settings-card glass-card" style="text-decoration: none; color: inherit; padding: 1.5rem; display: block; transition: transform 0.2s;">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="margin: 0; color: var(--text-primary);">Profile Settings</h3>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">Update your profile information and photo</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('settings.theme') }}" class="settings-card glass-card" style="text-decoration: none; color: inherit; padding: 1.5rem; display: block; transition: transform 0.2s;">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                        <path d="M12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm0-10c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0-4C6.48 4 2 8.48 2 14s4.48 10 10 10 10-4.48 10-10S17.52 4 12 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="margin: 0; color: var(--text-primary);">App Theme</h3>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">Switch between dark and light themes</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.settings-card:hover {
    transform: translateY(-2px);
}
.settings-card h3 {
    font-size: 1.1rem;
    font-weight: 600;
}
</style>
@endsection
