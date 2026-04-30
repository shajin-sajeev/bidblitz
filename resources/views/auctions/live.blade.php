@extends('layouts.app')

@section('content')
<div class="glass-card mb-4 flex justify-between items-center">
    <div>
        <h2>🔴 Live: {{ $auction->name }}</h2>
        <p>Real-time Bidding Engine</p>
    </div>
    <div>
        @if(auth()->id() === $auction->created_by)
            <button id="spin-btn" class="btn btn-primary" onclick="spinPlayer()">🎰 Spin Next Player</button>
        @else
            <div id="my-budget-display" style="background: var(--accent); font-size: 1.1rem; padding: 0.5rem 1rem; border-radius: 8px; color: white; font-weight: bold;">
                My Purse: ₹<span id="purse-amount">Loading...</span>
            </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-8">
    <!-- Active Player Area (2 cols) -->
    <div class="col-span-2">
        <div class="glass-card text-center" style="min-height: 400px; display: flex; flex-direction: column; justify-content: center;">
            <div id="waiting-screen">
                <h3 style="color: var(--text-muted);">Waiting for the next player...</h3>
            </div>
            
            <div id="active-player-screen" style="display: none;">
                <div style="background: var(--primary); padding: 4px 12px; border-radius: 12px; display: inline-block; margin-bottom: 1rem; font-size: 0.9rem; font-weight: bold;" id="player-role-badge">Role</div>
                <h1 id="player-name" style="font-size: 3rem; margin-bottom: 0.5rem; text-transform: uppercase;">Player Name</h1>
                <h3 style="color: var(--accent); margin-bottom: 2rem;">Base Price: ₹<span id="base-price">0</span></h3>
                
                <div class="grid grid-cols-4 gap-4 mb-8" style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                    <div><div style="color: var(--text-muted); font-size: 0.9rem;">Matches</div><strong id="stat-matches" style="font-size: 1.2rem;">0</strong></div>
                    <div><div style="color: var(--text-muted); font-size: 0.9rem;">Runs</div><strong id="stat-runs" style="font-size: 1.2rem;">0</strong></div>
                    <div><div style="color: var(--text-muted); font-size: 0.9rem;">Wickets</div><strong id="stat-wickets" style="font-size: 1.2rem;">0</strong></div>
                    <div><div style="color: var(--text-muted); font-size: 0.9rem;">Base</div><strong style="font-size: 1.2rem;">₹<span id="stat-base">0</span></strong></div>
                </div>

                <div style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 12px; border: 2px solid var(--accent);">
                    <div style="color: var(--text-muted); margin-bottom: 0.5rem;">Current Highest Bid</div>
                    <h2 id="current-bid-amount" style="font-size: 4rem; margin: 0; color: var(--accent); text-shadow: 0 0 20px rgba(0,242,254,0.5);">₹0</h2>
                    <div id="current-bid-team" style="font-size: 1.2rem; margin-top: 0.5rem;">Waiting for opening bid...</div>
                </div>
                
                <!-- Countdown Timer -->
                <div class="mt-8">
                    <div style="font-size: 1.2rem; color: var(--text-muted);">Time Remaining</div>
                    <div id="countdown-timer" style="font-size: 3.5rem; font-weight: bold; color: #ff4757; text-shadow: 0 0 10px rgba(255,71,87,0.5);">10</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bidding Controls / Activity (1 col) -->
    <div class="glass-card" style="display: flex; flex-direction: column;">
        <h3>Bidding Panel</h3>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
        
        @if(auth()->id() !== $auction->created_by)
            <div id="bidding-controls" style="display: none;">
                <p style="color: var(--text-muted); margin-bottom: 1rem;">Place your bid before the timer runs out!</p>
                <div class="grid grid-cols-2 gap-4">
                    <button class="btn" style="background: var(--primary); padding: 1.5rem; font-size: 1.5rem;" onclick="placeBid(5)">+5</button>
                    <button class="btn" style="background: var(--accent); padding: 1.5rem; font-size: 1.5rem;" onclick="placeBid(10)">+10</button>
                </div>
            </div>
        @else
            <div style="color: var(--text-muted); text-align: center; padding: 2rem 0;">
                You are the host. You cannot bid.
                <div class="mt-4 flex gap-4 justify-center">
                    <button class="btn btn-primary" onclick="markSold()">Sell Player</button>
                    <button class="btn" style="background: #ff4757; color: white;" onclick="markUnsold()">Unsold</button>
                </div>
            </div>
        @endif

        <div class="mt-8">
            <h4>Live Feed</h4>
            <div id="live-feed" style="max-height: 250px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; font-size: 0.9rem; margin-top: 1rem;">
                <!-- Feed items -->
                <div style="color: var(--text-muted); font-style: italic;">Auction live feed started...</div>
            </div>
        </div>
    </div>
</div>

<script>
    const auctionId = {{ $auction->id }};
    let currentBid = 0;
    let basePrice = 0;
    let timerInterval = null;
    let timeLeft = 10;
    let isFirstBid = true;
    
    document.addEventListener('DOMContentLoaded', () => {
        // Echo is available via app.js
        setTimeout(() => {
            if (window.Echo) {
                console.log("Listening to auction." + auctionId);
                window.Echo.channel('auction.' + auctionId)
                    .listen('PlayerSelected', (e) => {
                        showPlayer(e.playerData);
                        addFeedLog('System', `Player ${e.playerData.name} is up for auction!`);
                    })
                    .listen('BidPlaced', (e) => {
                        updateBid(e.bidData);
                        addFeedLog(e.bidData.team_name, `Placed bid of ₹${e.bidData.amount}`);
                    })
                    .listen('PlayerSold', (e) => {
                        clearPlayerScreen();
                        addFeedLog('System', `💰 ${e.message}`);
                    })
                    .listen('PlayerUnsold', (e) => {
                        clearPlayerScreen();
                        addFeedLog('System', `❌ ${e.message}`);
                    });
            } else {
                console.error("Laravel Echo is not loaded.");
            }
        }, 1000);
    });

    function clearPlayerScreen() {
        document.getElementById('active-player-screen').style.display = 'none';
        document.getElementById('waiting-screen').style.display = 'block';
        if(timerInterval) clearInterval(timerInterval);
        const controls = document.getElementById('bidding-controls');
        if(controls) controls.style.display = 'none';
    }

    function showPlayer(player) {
        document.getElementById('waiting-screen').style.display = 'none';
        document.getElementById('active-player-screen').style.display = 'block';
        
        document.getElementById('player-name').innerText = player.name;
        document.getElementById('player-role-badge').innerText = player.role;
        document.getElementById('base-price').innerText = player.base_price;
        
        document.getElementById('stat-matches').innerText = player.matches;
        document.getElementById('stat-runs').innerText = player.runs;
        document.getElementById('stat-wickets').innerText = player.wickets;
        document.getElementById('stat-base').innerText = player.base_price;

        basePrice = parseFloat(player.base_price);
        currentBid = basePrice; 
        isFirstBid = true;
        
        document.getElementById('current-bid-amount').innerText = '₹' + basePrice;
        document.getElementById('current-bid-team').innerText = 'Waiting for opening bid...';
        
        const controls = document.getElementById('bidding-controls');
        if(controls) controls.style.display = 'block';

        startTimer();
    }

    function updateBid(bidData) {
        currentBid = parseFloat(bidData.amount);
        isFirstBid = false;
        document.getElementById('current-bid-amount').innerText = '₹' + currentBid;
        document.getElementById('current-bid-team').innerText = bidData.team_name;
        
        startTimer();
    }

    function startTimer() {
        if(timerInterval) clearInterval(timerInterval);
        timeLeft = 10;
        document.getElementById('countdown-timer').innerText = timeLeft;
        document.getElementById('countdown-timer').style.color = '#ff4757';
        
        timerInterval = setInterval(() => {
            timeLeft--;
            document.getElementById('countdown-timer').innerText = timeLeft;
            if(timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('countdown-timer').innerText = "SOLD!";
                if(document.getElementById('bidding-controls')) {
                    document.getElementById('bidding-controls').style.display = 'none';
                }
            }
        }, 1000);
    }

    function placeBid(increment) {
        let amountToBid = isFirstBid ? basePrice : currentBid + increment;
            
        fetch(`/auctions/${auctionId}/bid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ amount: amountToBid })
        })
        .then(res => res.json())
        .then(data => {
            if(data.error) {
                alert(data.error);
            }
        });
    }

    function spinPlayer() {
        fetch(`/auctions/${auctionId}/spin`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.message) alert(data.message);
        });
    }

    function markSold() {
        fetch(`/auctions/${auctionId}/sell`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.error) alert(data.error);
        });
    }

    function markUnsold() {
        fetch(`/auctions/${auctionId}/unsold`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.error) alert(data.error);
        });
    }

    function addFeedLog(name, message) {
        const feed = document.getElementById('live-feed');
        const div = document.createElement('div');
        div.style.marginBottom = '0.5rem';
        div.innerHTML = `<strong style="color: var(--accent);">${name}:</strong> ${message}`;
        feed.prepend(div);
    }
</script>
@endsection
