<div class="container" style="max-width: 480px; padding-top: 6rem;">
    <div class="card" style="background: rgba(21, 26, 36, 0.6); backdrop-filter: blur(15px); border-radius: 16px; border: 1px solid var(--border-color); padding: 2.5rem;">
        <h2 style="text-align: center; margin-bottom: 2rem; font-weight: 800; font-size: 2rem;">
            <span class="gradient-text">{{ __('Player Login') }}</span>
        </h2>

        @if ($errorMessage)
            <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: #F87171; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.95rem; font-weight: 500;">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit.prevent="login">
            <div class="form-group">
                <label for="username" class="form-label">{{ __('Username') }}</label>
                <input type="text" id="username" wire:model.defer="username" class="form-control" placeholder="Contoh: ronaldo7" required autocomplete="username">
                @error('username') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input type="password" id="password" wire:model.defer="password" class="form-control" placeholder="••••••••" required>
                @error('password') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1.05rem; font-weight: 700; border-radius: 10px;">
                {{ __('Login') }}
            </button>
        </form>

        <p style="text-align: center; color: var(--text-muted); margin-top: 1.5rem; font-size: 0.9rem;">
            {{ __('Belum memiliki akun?') }}
            <a href="/register-player" style="color: var(--primary); text-decoration: none; font-weight: 600;">{{ __('Register') }}</a>
        </p>
    </div>
</div>
