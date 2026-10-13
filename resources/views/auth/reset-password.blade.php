<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fa-solid fa-lock"></i></div>
                <div class="auth-title">Reset Password</div>
                <div class="auth-subtitle">Buat password baru untuk akun Anda</div>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="auth-field">
                    <label for="email" class="auth-label">{{ __('Email') }}</label>
                    <input id="email" class="auth-input" type="email" name="email" value="{{ old('email', $request->email) }}"
                           placeholder="nama@email.com" required autofocus autocomplete="username">
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

                <div class="auth-row auth-row-end">
                    <button type="submit" class="auth-submit">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>