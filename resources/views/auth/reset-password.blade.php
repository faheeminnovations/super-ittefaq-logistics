<x-guest-layout>
    @php
        $title = 'Set New Password';
        $subtitle = 'Create a secure password for your account';
    @endphp
    <div class="reset-intro mb-4">
        <p>Reset your password to regain access to your account.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" 
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                   type="email" 
                   name="email" 
                   value="{{ old('email', $request->email) }}" 
                   required 
                   autofocus 
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
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                <input id="password" 
                       class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Enter your new password">
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
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input id="password_confirmation" 
                   class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm your new password">
            @if ($errors->has('password_confirmation'))
                <div class="text-danger mt-1" style="font-size: 12px;">
                    {{ $errors->first('password_confirmation') }}
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-shield-check me-2"></i> Reset Password
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
