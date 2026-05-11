@extends('layouts.app')

@section('body_class', 'login-page')

@section('content')
<div class="login-screen">
    <div class="login-shell">
        <section class="login-brand-panel" aria-label="Bid Blitz">
            <div class="login-brand-mark">BB</div>
            <div>
                <p class="login-kicker">Auction dashboard</p>
                <h1>Bid Blitz</h1>
                <p class="login-brand-copy">
                    Manage auctions, track teams, and jump back into bidding with a fast, focused experience.
                </p>
            </div>

            <div class="login-feature-grid">
                <div class="login-feature-card">
                    <span>Live</span>
                    <strong>Real-time bids</strong>
                </div>
                <div class="login-feature-card">
                    <span>Teams</span>
                    <strong>Smart tracking</strong>
                </div>
                <div class="login-feature-card">
                    <span>Mobile</span>
                    <strong>App-like view</strong>
                </div>
            </div>
        </section>

        <section class="login-form-card">
            <div class="login-mobile-brand">
                <div class="login-brand-mark">BB</div>
                <div>
                    <p class="login-kicker">Welcome to</p>
                    <h1>Bid Blitz</h1>
                </div>
            </div>

            <div class="login-heading">
                <h2>Welcome Back</h2>
                <p>Sign in with your username or phone number and password.</p>
            </div>

            <form action="{{ route('auth.login') }}" method="POST" class="login-form">
                @csrf
                <div class="form-group login-field">
                    <label for="login" class="form-label">Username or Phone Number</label>
                    <input type="text" name="login" id="login" class="form-control" placeholder="Enter username or phone number" required>
                    @error('login')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group login-field">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-note">
                    <span class="login-note-icon">i</span>
                    <span>You can login using either your username or phone number.</span>
                </div>

                <button type="submit" class="btn btn-primary btn-block login-submit">Sign In</button>
            </form>

            <div class="login-register">
                <p>Don't have an account? <a href="{{ route('auth.register') }}">Register</a></p>
            </div>
        </section>
    </div>
</div>

<style>
.login-screen {
    min-height: calc(100vh - 96px);
    display: grid;
    place-items: center;
    padding: 2rem 1rem;
}

.login-page .mobile-menu-toggle {
    display: none !important;
}

.login-shell {
    width: min(100%, 1040px);
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.045);
    box-shadow: 0 28px 70px rgba(0, 0, 0, 0.28);
}

.login-brand-panel,
.login-form-card {
    min-height: 610px;
}

.login-brand-panel {
    position: relative;
    display: grid;
    align-content: space-between;
    gap: 2rem;
    padding: 3rem;
    background:
        linear-gradient(135deg, rgba(251, 191, 36, 0.22), rgba(14, 165, 233, 0.12) 54%, rgba(15, 23, 42, 0.86)),
        var(--card-bg);
}

.login-brand-panel::before {
    content: "";
    position: absolute;
    inset: 0;
    border-left: 5px solid var(--primary);
    pointer-events: none;
}

.login-brand-mark {
    width: 62px;
    height: 62px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    background: var(--primary);
    color: #111827;
    font-weight: 900;
    font-size: 1.35rem;
    letter-spacing: 0.02em;
    box-shadow: 0 16px 32px rgba(251, 191, 36, 0.25);
}

.login-kicker {
    margin: 1.4rem 0 0.65rem;
    color: var(--primary);
    font-size: 0.8rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.login-brand-panel h1,
.login-mobile-brand h1 {
    margin: 0;
    color: var(--text-main);
    font-weight: 900;
    letter-spacing: 0;
}

.login-brand-panel h1 {
    font-size: clamp(3rem, 7vw, 5.25rem);
    line-height: 0.95;
}

.login-brand-copy {
    max-width: 470px;
    margin: 1.2rem 0 0;
    color: var(--text-muted);
    font-size: 1.05rem;
    line-height: 1.7;
}

.login-feature-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.85rem;
}

.login-feature-card {
    padding: 1rem;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.login-feature-card span {
    display: block;
    color: var(--primary);
    font-size: 0.72rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.35rem;
}

.login-feature-card strong {
    color: var(--text-main);
    font-size: 0.92rem;
}

.login-form-card {
    padding: 3rem;
    display: grid;
    align-content: center;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.025)),
        var(--card-bg);
}

.login-mobile-brand {
    display: none;
}

.login-heading {
    margin-bottom: 1.7rem;
}

.login-heading h2 {
    margin: 0 0 0.55rem;
    color: var(--text-main);
    font-size: clamp(2rem, 4vw, 2.7rem);
    font-weight: 900;
}

.login-heading p,
.login-register p {
    margin: 0;
    color: var(--text-muted);
    line-height: 1.6;
}

.login-form {
    display: grid;
    gap: 1rem;
}

.login-field {
    margin-bottom: 0;
}

.login-field .form-label {
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.55rem;
}

.login-field .form-control {
    min-height: 52px;
    border-radius: 16px;
    padding: 0.9rem 1rem;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: var(--text-main);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.login-field .form-control:focus {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(251, 191, 36, 0.72);
    box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.14);
}

.login-note {
    display: flex;
    align-items: flex-start;
    gap: 0.7rem;
    padding: 0.85rem 1rem;
    border-radius: 16px;
    background: rgba(251, 191, 36, 0.1);
    border: 1px solid rgba(251, 191, 36, 0.16);
    color: var(--text-muted);
    font-size: 0.9rem;
    line-height: 1.45;
}

.login-note-icon {
    flex: 0 0 auto;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: var(--primary);
    color: #111827;
    font-size: 0.78rem;
    font-weight: 900;
}

.login-submit {
    min-height: 52px;
    border-radius: 16px;
    font-size: 1rem;
    font-weight: 900;
    box-shadow: 0 16px 30px rgba(251, 191, 36, 0.2);
}

.login-register {
    margin-top: 1.4rem;
    text-align: center;
}

.login-register a {
    color: var(--primary);
    font-weight: 900;
    text-decoration: none;
}

.login-register a:hover {
    text-decoration: underline;
}

@media (max-width: 900px) {
    .login-screen {
        min-height: calc(100vh - 72px);
        padding: 0.9rem;
        align-items: start;
    }

    .login-shell {
        display: block;
        border-radius: 26px;
        overflow: visible;
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    .login-brand-panel {
        display: none;
    }

    .login-form-card {
        min-height: auto;
        padding: 1.15rem;
        border-radius: 26px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.26);
    }

    .login-mobile-brand {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        margin-bottom: 1.5rem;
        padding: 0.85rem;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .login-mobile-brand .login-brand-mark {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        font-size: 1.15rem;
    }

    .login-mobile-brand .login-kicker {
        margin: 0 0 0.2rem;
        font-size: 0.68rem;
    }

    .login-mobile-brand h1 {
        font-size: 1.75rem;
        line-height: 1;
    }

    .login-heading {
        margin-bottom: 1.2rem;
    }

    .login-heading h2 {
        font-size: 1.75rem;
    }

    .login-heading p {
        font-size: 0.93rem;
    }

    .login-field .form-control,
    .login-submit {
        min-height: 50px;
        border-radius: 15px;
    }

    .login-note {
        border-radius: 15px;
        font-size: 0.84rem;
    }
}

@media (max-width: 380px) {
    .login-screen {
        padding: 0.65rem;
    }

    .login-form-card {
        padding: 0.9rem;
        border-radius: 22px;
    }

    .login-mobile-brand {
        align-items: flex-start;
    }
}
</style>
@endsection
