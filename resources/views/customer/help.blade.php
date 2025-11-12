<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Butuh Bantuan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    @include('customer.partials.base-css')
    <style>
        :root { --bg:#f2f6ff; --primary:#4b88ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background: var(--bg); }
        
        .app { max-width: 420px; margin: 0 auto; min-height: 100vh; min-height: 100dvh; }
        .header { background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%); color:#fff; padding: 24px 16px; }
        .title { font-weight:700; font-size:20px; }
        .card { background: var(--card); border-radius: 16px; box-shadow: var(--shadow); padding: 14px; margin: -20px 16px 16px; }
        .label { display:block; font-size:12.5px; color: var(--muted); margin-bottom:6px; }
        .input, .select, textarea.input { width:100%; min-height:38px; padding:10px 12px; font-size:14px; border-radius:12px; border:1px solid #dbe4ff; background:#fff; outline:none; box-sizing: border-box; }
        textarea.input { min-height:84px; resize: vertical; overflow-wrap: break-word; word-break: break-word; }
        .actions { display:flex; gap:10px; margin-top: 8px; }
        .btn { flex:1; display:block; text-align:center; padding:12px 14px; border-radius:12px; text-decoration:none; }
        .btn-primary { background: var(--primary); color:#fff; }
        .btn-secondary { background: #eef3ff; color:#2a57c4; }
        /* Bottom Navigation: fixed agar tidak bergerak saat scroll */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .footer .bar { max-width: 420px; margin: 0 auto 8px; display:flex; justify-content: space-between; background:#fff; border-radius:18px; box-shadow: 0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        .footer .item { text-decoration:none; color: var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color: var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background: var(--primary); margin:2px auto 0; }
    </style>
</head>
<body>
    <div class="app">
        <div class="header">
            <div class="title">Butuh Bantuan</div>
        </div>
        <div class="card">
            @if(!$customer)
                <div style="margin-bottom:10px; background:#fff0f0; color:#7f1d1d; border:1px solid #fecaca; border-radius:12px; padding:10px;">Profil pelanggan belum ditemukan.</div>
            @endif

            <form method="POST" action="{{ route('customer.help.store') }}">
                @csrf
                <div class="field" style="margin-bottom:14px;">
                    <label class="label">Pilih Booking</label>
                    <select class="select" name="booking_id" required>
                        <option value="">-- Pilih salah satu --</option>
                        @foreach($bookings as $b)
                            <option value="{{ $b->id }}">#{{ $b->id }} · {{ optional($b->service)->name }} · {{ optional($b->scheduled_at)->format('d M Y H:i') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="margin-bottom:14px;">
                    <label class="label">Subject</label>
                    <input class="input" type="text" name="subject" value="{{ old('subject') }}" placeholder="Ringkas masalah" required>
                </div>

                <div class="field" style="margin-bottom:14px;">
                    <label class="label">Pesan</label>
                    <textarea class="input" name="message" rows="4" placeholder="Jelaskan detail kendala" required>{{ old('message') }}</textarea>
                </div>

                <!-- Prioritas dihilangkan sesuai permintaan -->

                <div class="actions">
                    <a href="{{ route('customer.home') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
</html>
