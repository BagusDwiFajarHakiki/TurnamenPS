<div class="container" style="max-width: 540px; padding-top: 4rem;">
    <div class="card" style="background: var(--bg-card); backdrop-filter: blur(15px); border-radius: 16px; border: 1px solid var(--border-color); padding: 2.5rem;">
        
        @if ($isRegistered)
            <div style="text-align: center; padding: 1.5rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1.5rem;">🎉</div>
                <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 1rem; color: var(--accent);">
                    {{ app()->getLocale() == 'id' ? 'Registrasi Akun Berhasil!' : 'Account Created!' }}
                </h2>
                <p style="color: var(--text-muted); font-size: 1rem; max-width: 440px; margin: 0 auto 2rem; line-height: 1.6;">
                    {{ $successMessage }}
                </p>

                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="/login" class="btn btn-primary" style="padding: 0.75rem 2rem;">{{ __('Login') }}</a>
                    <a href="/" class="btn btn-secondary" style="padding: 0.75rem 1.5rem;">{{ app()->getLocale() == 'id' ? 'Halaman Utama' : 'Home' }}</a>
                </div>
            </div>
        @else
            <h2 style="margin-bottom: 2rem; font-weight: 800; font-size: 2.25rem; text-align: center;">
                <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Daftar Akun Pemain' : 'Register Player Account' }}</span>
            </h2>

            <form wire:submit.prevent="register">
                <div class="form-group">
                    <label class="form-label" for="name">{{ app()->getLocale() == 'id' ? 'Nama Lengkap' : 'Full Name' }}</label>
                    <input type="text" id="name" wire:model.defer="name" class="form-control" placeholder="Contoh: Cristiano Ronaldo" required>
                    @error('name') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">{{ __('Username') }}</label>
                    <input type="text" id="username" wire:model.defer="username" class="form-control" placeholder="Contoh: cr7_king" required>
                    @error('username') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Nomor HP</label>
                    <input type="tel" id="phone" wire:model.defer="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                    <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                        {{ app()->getLocale() == 'id' ? 'Gunakan format nomor HP Indonesia yang valid.' : 'Use a valid Indonesian phone number format.' }}
                    </small>
                    @error('phone') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">{{ __('Password') }}</label>
                    <input type="password" id="password" wire:model.defer="password" class="form-control" placeholder="••••••••" required>
                    <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                        {{ app()->getLocale() == 'id' ? 'Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka.' : 'Minimum 8 characters, must contain uppercase, lowercase, and numbers.' }}
                    </small>
                    @error('password') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label" for="password_confirmation">{{ app()->getLocale() == 'id' ? 'Konfirmasi Password' : 'Confirm Password' }}</label>
                    <input type="password" id="password_confirmation" wire:model.defer="password_confirmation" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1.1rem;">
                    {{ app()->getLocale() == 'id' ? 'Buat Akun Pemain' : 'Create Player Account' }}
                </button>
            </form>

            <p style="text-align: center; color: var(--text-muted); margin-top: 1.5rem; font-size: 0.9rem;">
                {{ __('Sudah memiliki akun?') }}
                <a href="/login" style="color: var(--primary); text-decoration: none; font-weight: 600;">{{ __('Login') }}</a>
            </p>
        @endif

    </div>
</div>
