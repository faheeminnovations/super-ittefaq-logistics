<x-guest-layout>
    @php
        $title = 'Create Account';
        $subtitle = 'Join Super Ittefaq Logistics transport management system';
    @endphp
    <div class="register-intro mb-4">
        <p>Create your account to access the Super Ittefaq Logistics transport management system.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" 
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Enter your full name">
            @if ($errors->has('name'))
                <div class="text-danger mt-1" style="font-size: 12px;">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" 
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Enter your email address">
            @if ($errors->has('email'))
                <div class="text-danger mt-1" style="font-size: 12px;">
                    {{ $errors->first('email') }}
                </div>
            @endif
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input id="password" 
                       class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Create a password">
                <button class="btn btn-outline" type="button" id="togglePassword">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
            @if ($errors->has('password'))
                <div class="text-danger mt-1" style="font-size: 12px;">
                    {{ $errors->first('password') }}
                </div>
            @endif
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" 
                   class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm your password">
            @if ($errors->has('password_confirmation'))
                <div class="text-danger mt-1" style="font-size: 12px;">
                    {{ $errors->first('password_confirmation') }}
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('login') }}" class="text-link">
                Already have an account? Sign in
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i> Create Account
            </button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    });
    </script>
</x-guest-layout>
