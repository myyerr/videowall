<x-app-layout pageTitle="Pengaturan Profil">
    <x-slot name="slot">
        <div style="max-width: 680px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Update Profile Info -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-user" style="color:var(--accent);"></i>
                    <span class="card-title">Informasi Akun</span>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                            @error('name')
                                <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')
                                <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="display:flex;align-items:center;gap:12px;margin-top:20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Profil
                            </button>
                            @if (session('status') === 'profile-updated')
                                <span style="font-size:12px;color:var(--success);">✓ Profil berhasil diperbarui</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-lock" style="color:var(--accent);"></i>
                    <span class="card-title">Ganti Password</span>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <label class="form-label" for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="form-control"
                                   placeholder="••••••••" autocomplete="current-password">
                            @if($errors->updatePassword->get('current_password'))
                                <div style="color:var(--danger);font-size:12px;margin-top:4px;">
                                    {{ $errors->updatePassword->first('current_password') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password Baru</label>
                            <input type="password" id="password" name="password" class="form-control"
                                   placeholder="Minimal 8 karakter" autocomplete="new-password">
                            @if($errors->updatePassword->get('password'))
                                <div style="color:var(--danger);font-size:12px;margin-top:4px;">
                                    {{ $errors->updatePassword->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                                   placeholder="Ulangi password baru" autocomplete="new-password">
                            @if($errors->updatePassword->get('password_confirmation'))
                                <div style="color:var(--danger);font-size:12px;margin-top:4px;">
                                    {{ $errors->updatePassword->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>

                        <div style="display:flex;align-items:center;gap:12px;margin-top:20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-key"></i> Perbarui Password
                            </button>
                            @if (session('status') === 'password-updated')
                                <span style="font-size:12px;color:var(--success);">✓ Password berhasil diubah</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone: Delete Account -->
            <div class="card" style="border-color: rgba(239,68,68,0.25);">
                <div class="card-header" style="background: rgba(239,68,68,0.05);">
                    <i class="fa-solid fa-triangle-exclamation" style="color:var(--danger);"></i>
                    <span class="card-title" style="color:var(--danger);">Zona Berbahaya: Hapus Akun</span>
                </div>
                <div class="card-body">
                    <p style="font-size:12px;color:var(--text-2);margin-bottom:14px;line-height:1.5;">
                        Setelah akun dihapus, semua data terkait akan dihapus secara permanen. Pastikan Anda sudah membackup informasi yang dibutuhkan.
                    </p>

                    <form method="post" action="{{ route('profile.destroy') }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?')">
                        @csrf
                        @method('delete')

                        <div class="form-group">
                            <label class="form-label" for="del_password">Masukkan Password Anda untuk Konfirmasi</label>
                            <input type="password" id="del_password" name="password" class="form-control"
                                   placeholder="Password Anda" required style="max-width:320px;">
                            @if($errors->userDeletion->get('password'))
                                <div style="color:var(--danger);font-size:12px;margin-top:4px;">
                                    {{ $errors->userDeletion->first('password') }}
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-trash"></i> Hapus Akun Saya
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </x-slot>
</x-app-layout>
