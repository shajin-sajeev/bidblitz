@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 500px; margin: 2rem auto;">
    <h2 class="text-center">🔗 Join Auction</h2>
    <p class="text-center text-muted mb-8">Enter a Team Pass or Auction Pass to join.</p>

    <form action="{{ route('auctions.join.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="passcode" class="form-label">Passcode</label>
            <input type="text" name="passcode" id="passcode" class="form-control" placeholder="Enter passcode" required minlength="4">
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-4">Join</button>
    </form>
</div>
@endsection
