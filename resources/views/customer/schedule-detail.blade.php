<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Detail Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    @include('customer.partials.base-css')
    <style>
        :root { --bg:#f2f6ff; --primary:#4b88ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background: var(--bg); }
        .app { max-width: 960px; margin: 0 auto; min-height: 100vh; min-height: 100dvh; padding: 16px 16px 88px; }
        .header { background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%); color:#fff; padding: 16px; border-radius:16px; box-shadow: var(--shadow); }
        .title { font-weight:700; font-size:16px; }
        .card { background: var(--card); border-radius: 16px; box-shadow: var(--shadow); padding: 14px; margin-top:12px; }
        .row { display:flex; justify-content:space-between; font-size:13.5px; margin-bottom:6px; }
        .muted { color: var(--muted); font-size:12px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .footer .bar { max-width: 960px; margin: 0 auto 8px; display:flex; justify-content: space-between; background:#fff; border-radius:18px; box-shadow: 0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        @media (min-width: 768px) { .footer .bar { padding: 8px 10px; } }
        .footer .item { text-decoration:none; color: var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color: var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background: var(--primary); margin:2px auto 0; }
    </style>
    @include('customer.partials.base-js')
</head>
<body>
    <div class="app">
        <div class="header">
            <div class="title">Detail Order</div>
        </div>
        <div class="card">
            <div class="row"><span>Layanan</span><span>{{ optional($booking->service)->name ?? '-' }}</span></div>
            <div class="row"><span>Jadwal</span><span>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</span></div>
            <div class="row"><span>Alamat</span><span>{{ $booking->address }}</span></div>
            @if(optional($booking->cleaner)->id)
                <div class="row"><span>Petugas</span><span>{{ optional($booking->cleaner)->full_name ?? optional($booking->cleaner)->name }}</span></div>
            @endif
            <div class="row"><span>Status Order</span><span>{{ ucfirst($booking->status) }}</span></div>
            <div class="row" style="font-weight:700"><span>Total</span><span>Rp {{ number_format((float) ($booking->total_amount ?? 0), 0, ',', '.') }}</span></div>
        </div>

        

        @if($booking->status === 'completed')
        <div class="card">
            <div style="display:flex; gap:12px; align-items:center; margin-bottom:8px;">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Foto" style="width:48px; height:48px; object-fit:cover; border-radius:6px;">
                @else
                    <div style="width:48px; height:48px; border-radius:6px; background:#eef3ff; display:flex; align-items:center; justify-content:center; color:#7b8ca6;">N/A</div>
                @endif
                <div>
                    <div style="font-weight:600;">{{ optional($booking->cleaner)->full_name ?? optional($booking->cleaner)->name ?? '-' }}</div>
                    <div class="muted">Penilaian Petugas</div>
                </div>
            </div>
            @if(!$review)
            <form method="POST" action="{{ route('customer.schedule.review.store', ['booking' => $booking->id]) }}" id="reviewForm">
                @csrf
                @php($cur = (int) (optional($review)->rating ?? 0))
                <div class="stars" style="display:flex; gap:4px; margin-bottom:8px; align-items:center;">
                    @for($i=1; $i<=5; $i++)
                        <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" {{ $cur === $i ? 'checked' : '' }} style="position:absolute; left:-9999px;">
                        <label for="rating{{ $i }}" style="cursor:pointer;">
                            <i class="fa{{ $cur >= $i ? 's' : 'r' }} fa-star" style="font-size:22px; color:#f59e0b;"></i>
                        </label>
                    @endfor
                </div>
                <div style="margin-bottom:8px;">
                    <textarea name="comment" rows="3" placeholder="Tulis ulasan Anda..." style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:8px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn" style="width:100%;" disabled>Kirim Ulasan</button>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('reviewForm');
                    const submitBtn = document.getElementById('submitBtn');
                    const inputs = Array.from(document.querySelectorAll('input[name="rating"]'));
                    const icons = Array.from(document.querySelectorAll('.stars i'));
                    function updateStars(val) {
                        icons.forEach((icon, idx) => {
                            const filled = (idx + 1) <= val;
                            icon.classList.toggle('fas', filled);
                            icon.classList.toggle('far', !filled);
                        });
                    }
                    inputs.forEach((inp) => {
                        inp.addEventListener('change', () => {
                            const val = parseInt(inp.value);
                            updateStars(val);
                            if (val >= 1 && val <= 5) {
                                submitBtn.disabled = false;
                            }
                        });
                    });
                    const checked = inputs.find(i => i.checked);
                    updateStars(checked ? parseInt(checked.value) : 0);
                    if (checked) { submitBtn.disabled = false; }
                    form && form.addEventListener('submit', function() {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Mengirim...';
                    });
                });
            </script>
            @else
                <div class="muted">Anda sudah mengirim ulasan untuk order ini.</div>
            @endif
        </div>
        @endif

        <div class="card" style="display:flex; gap:8px;">
            <a href="{{ route('customer.schedule') }}" class="btn btn-secondary" style="flex:1; text-align:center; text-decoration:none; padding:10px 12px; border-radius:12px; background:#e6edff; color:#2a57c4; font-weight:600;">Kembali</a>
        </div>

        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
</html>
