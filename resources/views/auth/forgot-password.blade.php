<x-guest-layout>
    @php
        $title = 'Reset Password';
        $subtitle = 'Recover access to your account';
    @endphp
    <div class="forgot-intro mb-4">
        <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" 
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   placeholder="Enter your email address">
            @if ($errors->has('email'))
                <div class="text-danger mt-1" style="font-size: 12px;">
                    {{ $errors->first('email') }}
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('login') }}" class="text-link">
                <i class="bi bi-arrow-left me-1"></i> Back to login
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-2"></i> Send Reset Link
            </button>
        </div>
    </form>
</x-guest-layout>
