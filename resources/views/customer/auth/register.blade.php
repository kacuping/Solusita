<x-guest-layout>
    <div class="login-card">
        <!-- Form Registrasi Pelanggan -->
        <form method="POST" action="{{ route('customer.register.store') }}">
            @csrf

            <!-- Nama -->
            <div class="input-row mt-2">
                <!-- icon user -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 1114 0H3z" />
                </svg>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" required autofocus />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />

            <!-- Email -->
            <div class="input-row mt-4">
                <!-- icon mail -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M2 4a2 2 0 012-2h16a2 2 0 012 2v2l-10 6L2 6V4zm0 6l10 6 10-6v10a2 2 0 01-2 2H4a2 2 0 01-2-2V10z"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <!-- Telepon -->
            <div class="input-row mt-4">
                <!-- icon phone -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21c1.2.48 2.5.74 3.86.74a1 1 0 011 1v3.5a1 1 0 01-1 1C10.07 22 2 13.93 2 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.36.26 2.66.74 3.86a1 1 0 01-.21 1.11l-2.4 2.82z"/></svg>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="Nomor Telepon" autocomplete="tel" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />

            <!-- Alamat -->
            <div class="input-row mt-4">
                <!-- icon map -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                <input id="address" type="text" name="address" value="{{ old('address') }}" placeholder="Alamat" autocomplete="street-address" />
            </div>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />

            <!-- Password -->
            <div class="input-row mt-4">
                <!-- icon lock -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 8V6a5 5 0 0110 0v2h1a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2h1zm2-2a3 3 0 016 0v2H7V6z" clip-rule="evenodd" />
                </svg>
                <input id="password" type="password" name="password" placeholder="Kata Sandi" required autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <!-- Konfirmasi Password -->
            <div class="input-row mt-4">
                <!-- icon lock -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 8V6a5 5 0 0110 0v2h1a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2h1zm2-2a3 3 0 016 0v2H7V6z" clip-rule="evenodd" />
                </svg>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" required autocomplete="new-password" />
            </div>

            <button type="submit" class="login-button mt-6">Daftar</button>

            <div class="mt-4 text-sm text-center">
                <a href="{{ route('customer.login') }}" class="text-blue-700 hover:underline">Sudah punya akun? Masuk</a>
            </div>
        </form>
    </div>
</x-guest-layout>
