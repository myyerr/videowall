<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fa-solid fa-tv"></i></div>
                <div class="auth-title">Buat Akun Baru</div>
                <div class="auth-subtitle">Daftar untuk mengelola display dan jadwal</div>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="auth-field">
                    <label for="name" class="auth-label">{{ __('Name') }}</label>
                    <input id="name" class="auth-input" type="text" name="name" value="{{ old('name') }}"
                           placeholder="Nama lengkap" required autofocus autocomplete="name">
                    @error('name')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="auth-field">
                    <label for="email" class="auth-label">{{ __('Email') }}</label>
                    <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@email.com" required autocomplete="username">
                    @error('email')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <label for="password" class="auth-label">{{ __('Password') }}</label>
                    <input id="password" class="auth-input" type="password" name="password"
                           placeholder="••••••••" required autocomplete="new-password">
                    @error('password')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="auth-field">
                    <label for="password_confirmation" class="auth-label">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" class="auth-input" type="password" name="password_confirmation"
                           placeholder="••••••••" required autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-row">
                    <a class="auth-link" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <button type="submit" class="auth-submit">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>