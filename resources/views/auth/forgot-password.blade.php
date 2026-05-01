@extends('layouts.auth')

@section('title', 'Forgot Password - GDMS')

@section('content')
<form action="{{ route('password.email') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            value="{{ old('email') }}" 
            required
            class="form-input"
            placeholder="Enter your email address"
            autocomplete="email"
        >
        @error('email')
            <p style="color: #dc2626; font-size: 13px; margin-top: 6px;">{{ $message }}</p>
        @enderror
    </div>

    @if (session('status'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 14px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 24px;">
            {{ session('status') }}
        </div>
    @endif

    <button type="submit" class="btn-submit">
        Send Reset Link
    </button>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" style="color: #3b82f6; font-size: 14px; text-decoration: none; font-weight: 500;">
            &larr; Back to Login
        </a>
    </div>
</form>
@endsection
