@extends('layouts.auth')

@section('title', 'Login - GDMS')

@section('content')
<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="username" class="form-label">Username</label>
        <input
            type="text"
            name="username"
            id="username"
            value="{{ old('username') }}"
            required
            class="form-input"
            placeholder="Enter your username"
            autocomplete="username"
        >
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input 
            type="password" 
            name="password" 
            id="password" 
            required
            class="form-input"
            placeholder="Enter your password"
            autocomplete="current-password"
        >
    </div>

    <div style="text-align: right; margin-top: -16px; margin-bottom: 24px;">
        <a href="{{ route('password.request') }}" style="color: #3b82f6; font-size: 13px; text-decoration: none; font-weight: 500;">
            Forgot Password?
        </a>
    </div>

    <button type="submit" class="btn-submit">
        Sign In
    </button>
</form>
@endsection