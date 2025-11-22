<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Akun Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --bg: #f6f8fc;
            --text: #1f2d3d;
            --muted: #7b8ca6;
            --card: #ffffff;
            --primary: #4b88ff;
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }

        .app {
            max-width: 960px;
            margin: 0 auto;
            /* Gunakan unit viewport dinamis agar tidak muncul scroll mikro di mobile */
            min-height: 100vh;
            /* fallback */
            min-height: 100dvh;
            /* modern browsers */
            display: flex;
            flex-direction: column;
        }

        /* Header gradient dengan avatar */
        .header {
            position: relative;
            padding: 32px 18px 72px;
            color: #fff;
            background: linear-gradient(135deg, #ff7a59 0%, #ff477e 100%);
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 12px 24px rgba(255, 71, 126, 0.25);
        }

        .avatar {
            position: relative;
            width: 86px;
            height: 86px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.25);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 30;
        }

        .loading-overlay .spinner {
            width: 28px;
            height: 28px;
            border: 3px solid rgba(255, 255, 255, 0.6);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .handle {
            font-weight: 700;
            font-size: 18px;
            margin-top: 10px;
        }

        .email {
            opacity: 0.95;
            font-size: 13px;
        }

        /* Footer bottom navigation (samakan perilaku dengan halaman home: fixed) */
        /* Pindahkan ruang ekstra ke .content agar halaman tidak bertambah tinggi (menghindari scrollbar kanan) */
        .content {
            padding-bottom: calc(84px + env(safe-area-inset-bottom, 0));
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer .bar {
            max-width: 960px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.10);
            padding: 6px 6px;
            /* perkecil kiri-kanan */
            pointer-events: auto;
        }

        @media (min-width: 768px) {
            .footer .bar {
                padding: 8px 10px;
            }
        }

        .footer .item {
            text-decoration: none;
            color: var(--muted);
            text-align: center;
            font-size: 11px;
            flex: 1;
            /* distribusi item fleksibel */
            position: relative;
            padding: 4px 0;
            border-radius: 14px;
        }

        .footer .item .ico {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin: 0 auto 2px;
            color: var(--primary);
            font-size: 16px;
        }

        .footer .item.active {
            color: var(--text);
            font-weight: 600;
        }

        .footer .item.active::after {
            content: '';
            display: block;
            width: 18px;
            height: 1.5px;
            border-radius: 2px;
            background: var(--primary);
            margin: 2px auto 0;
        }

        .content {
            padding: 34px 18px 24px;
            flex: 1;
        }

        .section-title {
            font-weight: 700;
            color: var(--text);
            margin: 18px 6px;
            font-size: 16px;
        }

        /* Daftar informasi akun */
        .list {
            background: var(--card);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .row {
            position: relative;
            display: grid;
            grid-template-columns: 44px 1fr;
            align-items: center;
            padding: 14px 16px;
        }

        .row+.row {
            border-top: 1px solid #eceff5;
        }

        .row .ico {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #ffe9ef;
            color: #e45858;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .row .text {
            color: var(--text);
            font-weight: 600;
            font-size: 14px;
        }

        .row .sub {
            color: var(--muted);
            font-size: 12px;
        }

        .muted {
            color: var(--muted);
        }

        /* Inline edit UI */
        .edit {
            display: none;
        }

        .edit.active {
            display: block;
        }

        .view.hidden {
            display: none;
        }

        .actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            justify-content: flex-end;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 6px 10px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
        }

        .btn.save {
            background: var(--primary);
            color: #fff;
        }

        .btn.cancel {
            background: #eef2f7;
            color: var(--text);
        }

        /* Sedikit bayangan hanya pada tombol pop-up dan tombol aksi */
        .actions .btn,
        .popover .btn,
        .mini-popover .btn {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            padding: 5px 10px;
        }

        /* Popover avatar */
        .popover {
            position: absolute;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
            display: none;
            z-index: 20;
        }

        /* Posisi tombol di tengah avatar */
        .avatar .popover {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .popover.active {
            display: block;
        }

        .popover .row-btn {
            display: flex;
            gap: 8px;
        }

        .popover .btn {
            padding: 6px 10px;
        }

        /* Mini popover per baris untuk konfirmasi Ubah */
        .mini-popover {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
            display: none;
            z-index: 10;
        }

        .mini-popover.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="app">
        <div class="header">
            <div style="display:flex; gap:14px; align-items:center;">
                <div class="avatar">
                    @php
                        $initial = strtoupper(substr(auth()->user()->name ?? 'U', 0, 1));
                        $cust = $customer ?? \App\Models\Customer::where('user_id', auth()->id())->first();
                        $photoUrl = optional($cust)->avatar;
                        $useStream = is_string($photoUrl) && preg_match('#^/storage/#', $photoUrl);
                        $avatarSrc = $photoUrl
                            ? ($useStream && \Illuminate\Support\Facades\Route::has('customer.avatar')
                                ? route('customer.avatar', $cust)
                                : $photoUrl)
                            : null;
                    @endphp
                    @if ($avatarSrc)
                        <img src="{{ $avatarSrc }}?v={{ optional($cust)->updated_at?->timestamp ?? 0 }}"
                            alt="Avatar">
                    @else
                        <div
                            style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.15);color:#fff;font-size:36px;">
                            {{ $initial }}</div>
                    @endif
                    <div id="avatar-loading" class="loading-overlay">
                        <div class="spinner"></div>
                    </div>
                    <!-- Popover "Ubah Foto" diposisikan di tengah avatar -->
                    <div id="avatar-popover" class="popover" role="dialog" aria-label="Ubah Foto">
                        <div class="row-btn">
                            <button type="button" id="btn-avatar-upload" class="btn save">Ubah</button>
                        </div>
                        <form id="avatar-upload-form" method="POST" action="{{ route('customer.profile.avatar') }}"
                            enctype="multipart/form-data" style="display:none">
                            @csrf
                            <input type="file" name="avatar" id="avatar-file" accept="image/*">
                        </form>
                    </div>
                </div>
                <div>
                    <div class="handle">{{ '@' . (auth()->user()->name ?? 'user') }}</div>
                    <div class="email">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="section-title">Informasi Akun</div>
            @php
                $customer = $customer ?? \App\Models\Customer::where('user_id', auth()->id())->first();
            @endphp
            <div class="list">
                <div class="row">
                    <div class="ico"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <div class="text">Nama</div>
                        <div class="sub view" id="view-name">{{ auth()->user()->name ?? '-' }}</div>
                        <form class="edit" id="edit-name" method="POST"
                            action="{{ route('customer.profile.update') }}">
                            @csrf
                            <input type="text" name="name" value="{{ auth()->user()->name }}"
                                style="width:100%;padding:8px 10px;border:1px solid #dfe6ee;border-radius:8px;font-size:12px;">
                            <div class="actions">
                                <button type="button" class="btn cancel" data-cancel="name">Batal</button>
                                <button type="submit" class="btn save">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div class="mini-popover" id="popover-name"><button type="button" class="btn save"
                            data-edit="name">Ubah</button></div>
                </div>
                <div class="row">
                    <div class="ico" style="background:#eaf3ff; color:#3867d6"><i
                            class="fa-solid fa-mobile-screen-button"></i></div>
                    <div>
                        <div class="text">Nomor HP</div>
                        <div class="sub view" id="view-phone">{{ $customer->phone ?? ($customer->mobile ?? '-') }}</div>
                        <form class="edit" id="edit-phone" method="POST"
                            action="{{ route('customer.profile.update') }}">
                            @csrf
                            <input type="text" name="phone" value="{{ $customer->phone ?? $customer->mobile }}"
                                style="width:100%;padding:8px 10px;border:1px solid #dfe6ee;border-radius:8px;font-size:12px;">
                            <div class="actions">
                                <button type="button" class="btn cancel" data-cancel="phone">Batal</button>
                                <button type="submit" class="btn save">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div class="mini-popover" id="popover-phone"><button type="button" class="btn save"
                            data-edit="phone">Ubah</button></div>
                </div>
                <div class="row">
                    <div class="ico" style="background:#fff1d6; color:#d68b38"><i class="fa-regular fa-envelope"></i>
                    </div>
                    <div>
                        <div class="text">Email</div>
                        <div class="sub view" id="view-email">{{ auth()->user()->email ?? '-' }}</div>
                        <form class="edit" id="edit-email" method="POST"
                            action="{{ route('customer.profile.update') }}">
                            @csrf
                            <input type="email" name="email" value="{{ auth()->user()->email }}"
                                style="width:100%;padding:8px 10px;border:1px solid #dfe6ee;border-radius:8px;font-size:12px;">
                            <div class="actions">
                                <button type="button" class="btn cancel" data-cancel="email">Batal</button>
                                <button type="submit" class="btn save">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div class="mini-popover" id="popover-email"><button type="button" class="btn save"
                            data-edit="email">Ubah</button></div>
                </div>
                <div class="row">
                    <div class="ico" style="background:#e9fff1; color:#2f9e44"><i
                            class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <div class="text">Alamat</div>
                        <div class="sub view" id="view-address">{{ $customer->address ?? '-' }}</div>
                        <form class="edit" id="edit-address" method="POST"
                            action="{{ route('customer.profile.update') }}">
                            @csrf
                            <input type="text" name="address" value="{{ $customer->address }}"
                                style="width:100%;padding:8px 10px;border:1px solid #dfe6ee;border-radius:8px;font-size:12px;">
                            <div class="actions">
                                <button type="button" class="btn cancel" data-cancel="address">Batal</button>
                                <button type="submit" class="btn save">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div class="mini-popover" id="popover-address"><button type="button" class="btn save"
                            data-edit="address">Ubah</button></div>
                </div>
                <div class="row">
                    <div class="ico" style="background:#ffe9ef; color:#e45858"><i
                            class="fa-regular fa-calendar"></i></div>
                    <div>
                        <div class="text">Tanggal Lahir</div>
                        <div class="sub view" id="view-dob">{{ optional($customer->dob)->format('Y-m-d') ?? '-' }}
                        </div>
                        <form class="edit" id="edit-dob" method="POST"
                            action="{{ route('customer.profile.update') }}">
                            @csrf
                            <input type="date" name="dob"
                                value="{{ optional($customer->dob)->format('Y-m-d') }}"
                                style="width:100%;padding:8px 10px;border:1px solid #dfe6ee;border-radius:8px;font-size:12px;">
                            <div class="actions">
                                <button type="button" class="btn cancel" data-cancel="dob">Batal</button>
                                <button type="submit" class="btn save">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div class="mini-popover" id="popover-dob"><button type="button" class="btn save"
                            data-edit="dob">Ubah</button></div>
                </div>
            </div>

            <p class="muted" style="margin:14px 6px;">Data di atas diambil dari akun Anda. Untuk mengubah informasi,
                klik pada icon.</p>
            <div class="section-title">Aksi Akun</div>
            <div class="account-actions" style="display:flex;gap:8px;margin:14px 6px;">
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button class="btn" style="background:#e45858;color:#fff;">Logout</button>
                </form>
                <form method="POST" action="{{ route('customer.profile.destroy') }}"
                    onsubmit="return confirm('Hapus akun Anda secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button class="btn" style="background:#9b1c1c;color:#fff;">Hapus Akun</button>
                </form>
            </div>
        </div>
        <!-- Bottom bar: tampil di halaman profil, tombol Back to Home dihilangkan saat bottom nav ada -->

        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>



    <script>
        // Popover avatar: tampilkan tombol "Ubah" saja
        const avatar = document.querySelector('.avatar');
        const popover = document.getElementById('avatar-popover');
        if (avatar && popover) {
            avatar.addEventListener('click', () => popover.classList.add('active'));
            document.addEventListener('click', (e) => {
                if (!popover.contains(e.target) && !avatar.contains(e.target)) {
                    popover.classList.remove('active');
                }
            });
            const btnUpload = document.getElementById('btn-avatar-upload');
            const fileInput = document.getElementById('avatar-file');
            const formUpload = document.getElementById('avatar-upload-form');
            if (btnUpload && fileInput && formUpload) {
                btnUpload.addEventListener('click', () => {
                    fileInput.click();
                });
                fileInput.addEventListener('change', () => {
                    if (fileInput.files && fileInput.files.length > 0) {
                        const overlay = document.getElementById('avatar-loading');
                        if (overlay) overlay.style.display = 'flex';
                        formUpload.submit();
                        popover.classList.remove('active');
                    }
                });
            }
        }

        // Inline edit: aktifkan form edit untuk field tertentu
        function enableInlineEdit(field) {
            const view = document.getElementById(`view-${field}`);
            const edit = document.getElementById(`edit-${field}`);
            if (!view || !edit) return;
            view.classList.add('hidden');
            edit.classList.add('active');
        }

        function cancelInlineEdit(field) {
            const view = document.getElementById(`view-${field}`);
            const edit = document.getElementById(`edit-${field}`);
            if (!view || !edit) return;
            edit.classList.remove('active');
            view.classList.remove('hidden');
        }

        // Event: klik ikon/nilai -> tampilkan mini popover "Ubah"
        const rows = document.querySelectorAll('.list .row');
        rows.forEach((row) => {
            // Hanya ikon yang memunculkan popover "Ubah"
            const icons = row.querySelectorAll('.ico');
            const pop = row.querySelector('.mini-popover');
            icons.forEach(el => {
                el.style.cursor = 'pointer';
                el.addEventListener('click', () => {
                    // tutup popover lain
                    document.querySelectorAll('.mini-popover.active').forEach(p => p.classList
                        .remove('active'));
                    if (pop) pop.classList.add('active');
                });
            });
            // tombol Ubah pada popover
            const editBtn = row.querySelector('.mini-popover .btn.save');
            if (editBtn) {
                editBtn.addEventListener('click', (e) => {
                    const field = e.target.getAttribute('data-edit');
                    if (field) {
                        if (pop) pop.classList.remove('active');
                        enableInlineEdit(field);
                    }
                });
            }
            // tombol Batal pada form edit
            const cancelBtn = row.querySelector('.btn.cancel');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', (e) => {
                    const field = e.target.getAttribute('data-cancel');
                    if (field) cancelInlineEdit(field);
                });
            }
        });

        // klik di luar popover untuk menutup
        document.addEventListener('click', (e) => {
            document.querySelectorAll('.mini-popover.active').forEach(p => {
                if (!p.contains(e.target)) {
                    // pastikan tidak klik ikon yang memicu popover di baris sama
                    const row = p.closest('.row');
                    if (row && !row.contains(e.target)) {
                        p.classList.remove('active');
                    }
                }
            });
        });
    </script>
</body>

</html>
