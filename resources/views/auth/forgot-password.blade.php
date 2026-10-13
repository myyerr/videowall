<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fa-solid fa-key"></i></div>
                <div class="auth-title">Lupa Password</div>
                <div class="auth-subtitle">Kami akan kirimkan link reset ke email Anda</div>
            </div>

            <div class="auth-body-text">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="auth-field">
                    <label for="email" class="auth-label">{{ __('Email') }}</label>
                    <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@email.com" required autofocus>
                    @error('email')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-row auth-row-end">
                    <button type="submit" class="auth-submit">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>