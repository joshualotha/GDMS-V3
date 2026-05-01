@extends('layouts.auth')

@section('title', 'Reset Password - GDMS')

@section('content')
<form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <input 
            type="password" 
            name="password" 
            id="password" 
            required
            class="form-input"
            placeholder="Enter new password"
            autocomplete="new-password"
        >
        @error('password')
            <p style="color: #dc2626; font-size: 13px; margin-top: 6px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input 
            type="password" 
            name="password_confirmation" 
            id="password_confirmation" 
            required
            class="form-input"
            placeholder="Confirm new password"
            autocomplete="new-password"
        >
    </div>

    @if (session('status'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 14px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 24px;">
            {{ session('status') }}
        </div>
    @endif

    <button type="submit" class="btn-submit">
        Reset Password
    </button>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" style="color: #3b82f6; font-size: 14px; text-decoration: none; font-weight: 500;">
            &larr; Back to Login
        </a>
    </div>
</form>
@endsection
