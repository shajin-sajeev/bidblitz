@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 800px; margin: 0 auto;">
    <h2>Create New Auction</h2>
    <p>Setup your auction parameters below.</p>

    <form action="{{ route('auctions.store') }}" method="POST" class="mt-8">
        @csrf
        
        <div class="grid grid-cols-2">
            <div class="form-group">
                <label for="name" class="form-label">Auction Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="e.g. IPL 2026 Mega Auction" required>
            </div>
            
            <div class="form-group">
                <label for="sport" class="form-label">Sport</label>
                <select name="sport" id="sport" class="form-control" required>
                    <option value="" disabled selected>Select a Sport</option>
                    <option value="Cricket">Cricket</option>
                    <option value="Football">Football</option>
                    <option value="Kabaddi">Kabaddi</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="min_players" class="form-label">Min Players per Team</label>
                <input type="number" name="min_players" id="min_players" class="form-control" value="{{ old('min_players') }}" required min="1">
            </div>
            
            <div class="form-group">
                <label for="max_players" class="form-label">Max Players per Team</label>
                <input type="number" name="max_players" id="max_players" class="form-control" value="{{ old('max_players') }}" required min="1">
            </div>
            
            <div class="form-group">
                <label for="total_teams" class="form-label">Total Number of Teams</label>
                <input type="number" name="total_teams" id="total_teams" class="form-control" value="{{ old('total_teams') }}" required min="2">
            </div>
            
            <div class="form-group">
                <label for="budget" class="form-label">Total Budget per Team (₹)</label>
                <input type="number" name="budget" id="budget" class="form-control" value="{{ old('budget') }}" step="any" required>
            </div>
            
            <div class="form-group">
                <label for="min_amount" class="form-label">Minimum Amount for Player (₹)</label>
                <input type="number" name="min_amount" id="min_amount" class="form-control" value="{{ old('min_amount') }}" step="any" min="0" required>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <div id="max-base-price-hint" style="padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid var(--border-color); color: var(--text-muted);">
                    Enter budget and max players to calculate the maximum base price allowed for each player.
                </div>
            </div>
        </div>
        
        <div class="mt-4" style="text-align: right;">
            <a href="{{ route('dashboard') }}" class="btn" style="color: var(--text-muted); margin-right: 1rem;">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Auction & Generate Passes</button>
        </div>
    </form>
</div>
<script>
function showValidationModal(message, title = 'Validation Error') {
    if (window.modalSystem && typeof window.modalSystem.error === 'function') {
        window.modalSystem.error(message, title);
        return;
    }

    alert(message);
}

function updateMaxBasePriceHint() {
    const budget = Number(document.getElementById('budget')?.value || 0);
    const maxPlayers = Number(document.getElementById('max_players')?.value || 0);
    const minAmountInput = document.getElementById('min_amount');
    const hint = document.getElementById('max-base-price-hint');

    if (!hint || budget <= 0 || maxPlayers <= 0) {
        if (hint) {
            hint.textContent = 'Enter budget and max players to calculate the maximum base price allowed for each player.';
        }
        if (minAmountInput) {
            minAmountInput.removeAttribute('max');
        }
        return;
    }

    const maxBasePrice = budget / maxPlayers;
    const formatted = maxBasePrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    hint.textContent = `Maximum base price per player for this auction: Rs. ${formatted}`;

    if (minAmountInput) {
        minAmountInput.title = `Minimum amount cannot exceed Rs. ${formatted}`;
    }
}

['budget', 'max_players', 'min_amount'].forEach((id) => {
    document.getElementById(id)?.addEventListener('input', updateMaxBasePriceHint);
});

updateMaxBasePriceHint();

document.querySelector('form[action="{{ route('auctions.store') }}"]')?.addEventListener('submit', function(event) {
    const budget = Number(document.getElementById('budget')?.value || 0);
    const maxPlayers = Number(document.getElementById('max_players')?.value || 0);
    const minAmount = Number(document.getElementById('min_amount')?.value || 0);

    if (budget <= 0 || maxPlayers <= 0) {
        return;
    }

    const maxBasePrice = budget / maxPlayers;
    if (minAmount > maxBasePrice) {
        event.preventDefault();
        const formatted = maxBasePrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        showValidationModal(`Minimum amount is too high for this budget. Maximum base price per player is Rs. ${formatted}.`);
    }
});
</script>
@endsection
