<x-guest-layout>
    <div class="login-card">
        <!-- Label dihapus sesuai permintaan -->

        <!-- Status sesi -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('customer.login.store') }}">
            @csrf

            <!-- Email -->
            <div class="input-row mt-2">
                <!-- icon user -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 1114 0H3z" />
                </svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <!-- Password -->
            <div class="input-row mt-6">
                <!-- icon lock -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 8V6a5 5 0 0110 0v2h1a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2h1zm2-2a3 3 0 016 0v2H7V6z" clip-rule="evenodd" />
                </svg>
                <input id="password" type="password" name="password" placeholder="Kata Sandi" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <div class="login-actions">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-700 hover:underline">Lupa Password?</a>
                @endif
            </div>

            <button type="submit" class="login-button mt-6">Masuk</button>

            <!-- Opsi registrasi pelanggan dan tautan bantuan -->
            <div class="mt-4 flex items-center justify-between text-sm">
                <a href="{{ route('customer.register') }}" class="text-blue-700 hover:underline">Belum punya akun? Daftar</a>
                <a href="mailto:support@solusita.local" class="text-gray-700 hover:underline">Butuh bantuan?</a>
            </div>
        </form>
    </div>
</x-guest-layout>
