@extends('layouts.auth')

@section('title', 'Login - GDMS')

@section('content')
<form action="{{ route('login') }}" method="POST">
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

    <button type="submit" class="btn-submit">
        Sign In
    </button>
</form>
@endsection