<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fa-solid fa-tv"></i></div>
                <div class="auth-title">Masuk ke E-Banner</div>
                <div class="auth-subtitle">Kelola display TV, jadwal tayang, dan media prodi</div>
                <a class="auth-link" href="{{ route('register') }}" style="margin-top:4px;">
                    {{ __('Belum punya akun? Daftar') }}
                </a>
            </div>

            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="auth-field">
                    <label for="email" class="auth-label">{{ __('Email') }}</label>
                    <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@email.com" required autofocus autocomplete="username">
                    @error('email')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <label for="password" class="auth-label">{{ __('Password') }}</label>
                    <input id="password" class="auth-input" type="password" name="password"
                           placeholder="••••••••" required autocomplete="current-password">
                    @error('password')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="auth-remember">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">{{ __('Remember me') }}</label>
                </div>

                <div class="auth-row">
                    @if (Route::has('password.request'))
                        <a class="auth-link" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @else
                        <span></span>
                    @endif

                    <button type="submit" class="auth-submit">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>