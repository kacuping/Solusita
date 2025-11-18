<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
    <title>Pesan Layanan - {{ $service->name }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        :root {
            --bg: #f2f6ff;
            --primary: #4b88ff;
            --primary-dark: #3867d6;
            --text: #1f2d3d;
            --muted: #7b8ca6;
            --card: #fff;
            --shadow: 0 10px 20px rgba(0, 0, 0, .08);
            --footer-height: 72px;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* App container: beri padding bottom untuk menghindari tertutup footer */
        .app {
            max-width: 420px;
            margin: 0 auto;
            min-height: 100vh;
            /* fallback */
            min-height: 100dvh;
            /* modern browsers */
            padding-bottom: calc(var(--footer-height) + env(safe-area-inset-bottom, 0) + 12px);
            /* sedikit padding atas agar header tidak nempel dengan status bar saat inspect */
        }

        /* Header */
        .header {
            background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%);
            color: #fff;
            padding: 20px 16px 40px;
        }

        .header .title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.1px;
        }

        /* Summary card */
        .summary {
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 14px;
            margin: -28px 16px 12px;
            /* card 'floating' effect */
        }

        .summary .title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .meta {
            color: var(--muted);
            font-size: 13px;
        }

        .price {
            color: var(--primary);
            font-weight: 700;
            margin-top: 8px;
            font-size: 15px;
        }

        /* Form */
        form {
            padding: 0 16px 10px;
        }

        .field {
            margin-bottom: 14px;
        }

        .label {
            display: block;
            font-size: 12.5px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        /* Inputs: gunakan box-sizing dan tinggi adaptif */
        .input,
        .select,
        textarea.input {
            width: 100%;
            min-height: 38px;
            height: auto;
            padding: 10px 12px;
            font-size: 14px;
            border-radius: 12px;
            border: 1px solid #dbe4ff;
            background: #fff;
            outline: none;
            line-height: 1.3;
            box-shadow: none;
        }

        textarea.input {
            min-height: 84px;
            resize: vertical;
        }

        .input:focus,
        .select:focus,
        textarea.input:focus {
            border-color: #b7ccff;
            box-shadow: 0 0 0 4px rgba(75, 136, 255, 0.10);
        }

        /* Row / columns */
        .row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .row .col {
            flex: 1;
            min-width: 0;
        }

        /* mencegah overflow */

        /* Date/time khusus */
        .dt-row {
            align-items: stretch;
            gap: 10px;
        }

        .dt-row .input {
            height: 40px;
            padding: 8px 10px;
            font-size: 13px;
            border-radius: 10px;
        }

        /* Buat kolom waktu lebih kecil namun responsif */
        .dt-row .col:first-child {
            flex: 1 1 0;
        }

        .dt-row .col:last-child {
            flex: 0 0 150px;
            max-width: 150px;
        }

        /* Input group untuk icon */
        .input-group {
            position: relative;
        }

        .input-group .fld-ico {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa6c2;
            font-size: 16px;
            pointer-events: none;
        }

        .input-group .input {
            padding-right: 38px;
        }

        /* Tombol aksi */
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .btn {
            flex: 1;
            display: inline-block;
            text-align: center;
            height: 44px;
            line-height: 44px;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border: none;
            box-shadow: 0 6px 12px rgba(75, 136, 255, 0.12);
        }

        .btn-secondary {
            background: #eef3ff;
            color: #2a57c4;
            text-decoration: none;
        }

        /* Errors */
        .errors {
            margin: 0 16px 12px;
            background: #fff0f0;
            color: #7f1d1d;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 10px;
        }

        /* Bottom Navigation (fixed) */
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: auto;
        }

        .footer .bar {
            max-width: 420px;
            margin: 0 auto calc(env(safe-area-inset-bottom, 8px));
            display: flex;
            justify-content: space-between;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, .10);
            padding: 8px 8px;
            height: var(--footer-height);
            align-items: center;
        }

        .footer .item {
            text-decoration: none;
            color: var(--muted);
            text-align: center;
            font-size: 11px;
            flex: 1;
            padding: 4px 6px;
            border-radius: 14px;
        }

        .footer .item .ico {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin: 0 auto 4px;
            color: #2a57c4;
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

        /* Small screens */
        @media (max-width:380px) {
            .header {
                padding: 16px 12px 34px;
            }

            .summary {
                margin: -24px 12px 10px;
                padding: 12px;
                border-radius: 14px;
            }

            .footer .bar {
                margin: 0 auto calc(env(safe-area-inset-bottom, 8px));
                padding: 6px;
                height: 64px;
            }

            .dt-row .col:last-child {
                flex: 0 0 120px;
                max-width: 120px;
            }

            .input {
                font-size: 13.5px;
            }
        }

        @media (max-width:320px) {
            .footer .bar {
                padding: 6px 4px;
                border-radius: 14px;
            }

            .dt-row {
                gap: 8px;
            }

            .dt-row .col:last-child {
                flex: 0 0 110px;
                max-width: 110px;
            }
        }

        /* Remove spinner on date/time for consistent look on some browsers */
        input[type="date"],
        input[type="time"] {
            -webkit-appearance: none;
            appearance: none;
        }
    </style>
</head>

<body>
    <script>
        (function() {
            var F = new Intl.NumberFormat('id-ID');
            window.recalc = window.recalc || function() {};
            window.updateM2 = function(i) {
                try {
                    var b = Number(i.getAttribute('data-base') || 0);
                    var row = i.closest('.row') || document;
                    var L = Number(((row.querySelector('input[name="length_m"]') || {
                        value: 0
                    }).value) || 0);
                    var W = Number(((row.querySelector('input[name="width_m"]') || {
                        value: 0
                    }).value) || 0);
                    var area = Math.max(L * W, 0);
                    var x = Math.max(b * area, 0);
                    var eb = document.getElementById('amount_base');
                    if (eb) eb.textContent = 'Rp ' + F.format(x);
                    var et = document.getElementById('amount_total');
                    if (et) et.textContent = 'Rp ' + F.format(x);
                } catch (e) {}
                if (window.recalc) window.recalc();
            };
        })();
    </script>
    <div class="app">
        <div class="header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="title">Pesan Layanan</div>
            </div>
        </div>

        <div class="summary">
            <div class="title">{{ $service->name }}</div>
            @php($isDuration = strtolower(trim((string) ($service->unit_type ?? 'Durasi'))) === 'durasi')
            <div class="meta">
                @if ($isDuration && (int) ($service->duration_minutes ?? 0) > 0)
                    Durasi: {{ (int) $service->duration_minutes }} menit
                @else
                    Satuan/QTY: {{ $service->unit_type ?? 'Satuan' }}
                @endif
                · Deskripsi:
                {{ $service->description ?? 'Belum ada deskripsi.' }}
            </div>
            <div class="price">Rp {{ number_format($service->base_price, 0, ',', '.') }}</div>
        </div>

        @if ($errors->any())
            <div class="errors">
                <div style="font-weight:600; margin-bottom:6px;">Periksa kembali:</div>
                <ul style="margin:0 0 0 18px; padding:0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.order.store') }}">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}" />

            <div class="field">
                <label class="label">Tanggal & Waktu</label>
                <div class="row dt-row">
                    <div class="col">
                        <div class="input-group">
                            <input class="input" type="date" name="date"
                                value="{{ old('date', now()->format('Y-m-d')) }}" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <input class="input" type="time" name="time" value="{{ old('time', '09:00') }}"
                                required autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="field" id="dp_section" style="display:none;">
                <label class="label">Pembayaran DP</label>
                <div class="input-group" style="display:block;font-size:14px;">
                    <div style="margin-bottom:6px;">Konfirmasi pembayaran DP Rp {{ number_format($dpAmount ?? 50000, 0, ',', '.') }} diperlukan untuk jadwal di hari berbeda.</div>
                    @if (!empty($dpOption))
                        <div style="margin-bottom:6px;">Rekening: {{ $dpOption['bank_name'] ?? '' }} {{ $dpOption['bank_account_number'] ?? '' }} a/n {{ $dpOption['bank_account_name'] ?? '' }}</div>
                    @else
                        <div class="alert alert-warning" style="padding:6px 10px;">Nomor rekening belum tersedia. Hubungi admin.</div>
                    @endif
                    <div class="form-check" style="margin-top:8px;">
                        <input class="form-check-input" type="checkbox" name="agree_dp" id="agree_dp" value="1" {{ old('agree_dp') ? 'checked' : '' }}>
                        <label class="form-check-label" for="agree_dp" style="font-size:14px;">Saya setuju dan akan membayar DP sebesar Rp {{ number_format($dpAmount ?? 50000, 0, ',', '.') }}</label>
                    </div>
                </div>
            </div>

            @php($unitType = trim((string) ($service->unit_type ?? 'Satuan')))
            @if ($isDuration)
                <div class="field">
                    <label class="label">Durasi</label>
                    <div class="input-group">
                        @php($minBase = max((int) ($minMinutes ?? ($service->duration_minutes ?? 60)), 60))
                        @php($start = max(120, $minBase))
                        @php($end = max($start, 360))
                        <select class="select" id="duration_minutes" name="duration_minutes" required
                            data-base="{{ (float) ($service->base_price ?? 0) }}"
                            onchange="window.recalc && window.recalc();(function(s){var b=Number(s.getAttribute('data-base')||0);var d=Number(s.value||0);var u=60;var x=Math.max(b*(d/u),0);var f=new Intl.NumberFormat('id-ID');var eb=document.getElementById('amount_base');if(eb)eb.textContent='Rp '+f.format(x);var et=document.getElementById('amount_total');if(et)et.textContent='Rp '+f.format(x);})(this)"
                            oninput="window.recalc && window.recalc();(function(s){var b=Number(s.getAttribute('data-base')||0);var d=Number(s.value||0);var u=60;var x=Math.max(b*(d/u),0);var f=new Intl.NumberFormat('id-ID');var eb=document.getElementById('amount_base');if(eb)eb.textContent='Rp '+f.format(x);var et=document.getElementById('amount_total');if(et)et.textContent='Rp '+f.format(x);})(this)">
                            @for ($m = $start; $m <= $end; $m += 60)
                                <option value="{{ $m }}"
                                    {{ (int) old('duration_minutes', $start) === $m ? 'selected' : '' }}>
                                    {{ $m }} menit ({{ number_format($m / 60, 0) }} jam)
                                </option>
                            @endfor
                        </select>
                        <span class="fld-ico"><i class="fa-solid fa-clock"></i></span>
                    </div>
                    @if (isset($minMinutes) && $minMinutes > 0)
                        <div style="font-size:12px; color:#7b8ca6; margin-top:4px;">Minimal {{ $minMinutes }} menit
                            (sesuai deskripsi layanan)</div>
                    @endif
                </div>
            @else
                @if (strtoupper($unitType) === 'M2')
                    <div class="field">
                        <label class="label">Ukuran</label>
                        <div class="row dt-row">
                            <div class="col">
                                <div class="input-group">
                                    <input class="input" type="number" name="length_m" value="{{ old('length_m') }}"
                                        min="0.1" step="0.1" placeholder="Panjang (m)" autocomplete="off"
                                        data-base="{{ (float) ($service->base_price ?? 0) }}"
                                        onchange="window.updateM2 && window.updateM2(this)"
                                        oninput="window.updateM2 && window.updateM2(this)" />
                                    <span class="fld-ico"><i class="fa-solid fa-ruler-horizontal"></i></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <input class="input" type="number" name="width_m" value="{{ old('width_m') }}"
                                        min="0.1" step="0.1" placeholder="Lebar (m)" autocomplete="off"
                                        data-base="{{ (float) ($service->base_price ?? 0) }}"
                                        onchange="window.updateM2 && window.updateM2(this)"
                                        oninput="window.updateM2 && window.updateM2(this)" />
                                    <span class="fld-ico"><i class="fa-solid fa-ruler-combined"></i></span>
                                </div>
                            </div>
                        </div>
                        <div style="font-size:12px; color:#7b8ca6; margin-top:4px;">Masukkan ukuran dalam meter (m).
                        </div>
                    </div>
                @else
                    <div class="field">
                        <label class="label">Satuan/QTY</label>
                        <div class="input-group">
                            <input class="input" type="number" name="qty" value="{{ old('qty', 1) }}"
                                min="1" step="1" autocomplete="off"
                                data-base="{{ (float) ($service->base_price ?? 0) }}"
                                onchange="window.recalc && window.recalc();(function(i){var b=Number(i.getAttribute('data-base')||0);var q=Math.max(Number(i.value||1),1);var x=Math.max(b*q,0);var f=new Intl.NumberFormat('id-ID');var eb=document.getElementById('amount_base');if(eb)eb.textContent='Rp '+f.format(x);var et=document.getElementById('amount_total');if(et)et.textContent='Rp '+f.format(x);})(this)"
                                oninput="window.recalc && window.recalc();(function(i){var b=Number(i.getAttribute('data-base')||0);var q=Math.max(Number(i.value||1),1);var x=Math.max(b*q,0);var f=new Intl.NumberFormat('id-ID');var eb=document.getElementById('amount_base');if(eb)eb.textContent='Rp '+f.format(x);var et=document.getElementById('amount_total');if(et)et.textContent='Rp '+f.format(x);})(this)" />
                            <span class="fld-ico"><i class="fa-solid fa-list-ol"></i></span>
                        </div>
                        <div style="font-size:12px; color:#7b8ca6; margin-top:4px;">Satuan: {{ $unitType }}</div>
                    </div>
                @endif
            @endif

            <div class="field">
                <label class="label">Alamat</label>
                <textarea class="input" name="address" rows="3" required placeholder="Tulis alamat lengkap">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="field">
                <label class="label">Catatan (opsional)</label>
                <textarea class="input" name="notes" rows="3" placeholder="Contoh: akses parkir, preferensi petugas">{{ old('notes') }}</textarea>
            </div>

            <div class="field">
                <label class="label">Kode Promo (opsional)</label>
                <div class="input-group">
                    <input class="input" type="text" name="promotion_code" value="{{ old('promotion_code') }}"
                        placeholder="Masukkan kode" autocomplete="off" />
                    <span class="fld-ico"><i class="fa-solid fa-tag"></i></span>
                </div>
            </div>

            <div class="field">
                <label class="label">Ringkasan Pembayaran</label>
                @php($initSubtotal = (float) ($service->base_price ?? 0))
                @php($unitType = trim((string) ($service->unit_type ?? 'Satuan')))
                @if ($isDuration)
                    @php($minBase = max((int) ($minMinutes ?? ($service->duration_minutes ?? 60)), 60))
                    @php($start = max(120, $minBase))
                    @php($selectedMinutes = (int) old('duration_minutes', $start))
                    @php($initSubtotal = max($initSubtotal * ($selectedMinutes / 60), 0))
                @elseif (strtoupper($unitType) === 'M2')
                    @php($L = (float) old('length_m', 0))
                    @php($W = (float) old('width_m', 0))
                    @php($area = max($L * $W, 0))
                    @php($initSubtotal = max($initSubtotal * $area, 0))
                @else
                    @php($qty = max((int) old('qty', 1), 1))
                    @php($initSubtotal = max($initSubtotal * $qty, 0))
                @endif
                <div class="input-group" style="display:block;font-size:14px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:14px;">
                        <span>Jumlah Pembayaran</span>
                        <span id="amount_base">Rp {{ number_format($initSubtotal, 0, ',', '.') }}</span>
                    </div>
                    <div id="amount_calc" style="text-align:right; font-size:12px; color:#7b8ca6; margin:-2px 0 6px;">
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:14px;">
                        <span>Promo</span>
                        <span id="amount_discount">Rp 0</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-weight:600;font-size:14px;">
                        <span>Total Pembayaran</span>
                        <span id="amount_total">Rp {{ number_format($initSubtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>


            @php($initialAgree = (bool) old('agree_dp'))
            <div class="field" id="pm_section" style="{{ !$initialAgree ? 'display:none;' : '' }}">
                <label class="label">Metode Pembayaran</label>
                <div class="input-group" style="display:block;font-size:14px;">
                    @php($hasAny = !empty($cashActive))
                    @if (!empty($cashActive))
                        <div class="form-check" style="margin-bottom:6px;">
                            <input class="form-check-input" type="radio" name="payment_method" id="pm_cash"
                                value="cash" {{ old('payment_method') === 'cash' ? 'checked' : '' }}>
                            <label class="form-check-label" for="pm_cash" style="font-size:14px;">Tunai
                                (Cash)</label>
                        </div>
                    @endif
                    @foreach ($paymentOptions ?? [] as $opt)
                        @if (!empty($opt['active']))
                            @php($hasAny = true)
                            <div class="form-check" style="margin-bottom:6px;">
                                <input class="form-check-input" type="radio" name="payment_method"
                                    id="pm_{{ $opt['id'] }}" value="option_{{ $opt['id'] }}"
                                    {{ old('payment_method') === 'option_' . $opt['id'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="pm_{{ $opt['id'] }}"
                                    style="font-size:14px;">
                                    {{ $opt['label'] }}
                                    @if (($opt['type'] ?? '') === 'transfer' && ($opt['bank_name'] ?? null))
                                        · {{ $opt['bank_name'] }}
                                    @endif
                                </label>
                            </div>
                        @endif
                    @endforeach
                    @unless ($hasAny)
                        <div class="alert alert-warning" style="padding:6px 10px;">Belum ada metode pembayaran aktif.
                            Hubungi admin.</div>
                    @endunless
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('customer.service.show', $service->slug) }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary" id="btn-submit">Konfirmasi Pesanan</button>
            </div>
        </form>

        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
    <script>
        (function() {
            function formatIDR(n) {
                try {
                    n = Math.round(Number(n));
                } catch (e) {
                    n = 0;
                }
                return new Intl.NumberFormat('id-ID').format(n);
            }
            const base = Number({{ (float) ($service->base_price ?? 0) }});
            const unitMinutes = Number({{ (int) ($service->duration_minutes ?? 60) }} || 60);
            const isDuration = Boolean(
                {{ json_encode(strtolower(trim((string) ($service->unit_type ?? 'Durasi'))) === 'durasi') }});
            const unitType = String({{ json_encode(strtoupper(trim((string) ($service->unit_type ?? 'SATUAN')))) }});
            const promoInput = document.querySelector('input[name="promotion_code"]');
            const durationInput = document.getElementById('duration_minutes') || document.querySelector(
                '[name="duration_minutes"]');
            const lenInput = document.querySelector('input[name="length_m"]');
            const widInput = document.querySelector('input[name="width_m"]');
            const qtyInput = document.querySelector('input[name="qty"]');
            const svc = document.querySelector('input[name="service_id"]').value;
            const elBase = document.getElementById('amount_base');
            const elDisc = document.getElementById('amount_discount');
            const elTotal = document.getElementById('amount_total');

            function calcSubtotal() {
                if (isDuration) {
                    const d = Number(durationInput && durationInput.value || 0);
                    const u = 60;
                    return Math.max(base * (d / u), 0);
                }
                if (unitType === 'M2') {
                    const L = Number(lenInput && lenInput.value || 0);
                    const W = Number(widInput && widInput.value || 0);
                    const area = Math.max(L * W, 0);
                    return Math.max(base * area, 0);
                }
                const q = Number(qtyInput && qtyInput.value || 1);
                return Math.max(base * Math.max(q, 1), 0);
            }

            function calcFormula() {
                if (isDuration) {
                    const d = Number(durationInput && durationInput.value || 0);
                    const factor = Math.max(d / 60, 0);
                    const factorStr = Number.isInteger(factor) ? String(factor) : factor.toFixed(2);
                    return 'Rp ' + formatIDR(base) + ' * ' + factorStr;
                }
                if (unitType === 'M2') {
                    const L = Number(lenInput && lenInput.value || 0);
                    const W = Number(widInput && widInput.value || 0);
                    const area = Math.max(L * W, 0);
                    const areaStr = Number.isInteger(area) ? String(area) : area.toFixed(2);
                    return 'Rp ' + formatIDR(base) + ' * ' + areaStr + ' m²';
                }
                const q = Math.max(Number(qtyInput && qtyInput.value || 1), 1);
                return 'Rp ' + formatIDR(base) + ' * ' + q;
            }
            async function recalc() {
                const subtotal = calcSubtotal();
                elBase.textContent = 'Rp ' + formatIDR(subtotal);
                const elCalc = document.getElementById('amount_calc');
                if (elCalc) {
                    elCalc.textContent = calcFormula();
                }
                const code = (promoInput && promoInput.value || '').trim();
                if (!code) {
                    elDisc.textContent = 'Rp 0';
                    elTotal.textContent = 'Rp ' + formatIDR(subtotal);
                    return;
                }
                try {
                    const baseUrl = @json(route('customer.promo.validate'));
                    const params = new URLSearchParams({
                        service_id: svc,
                        code,
                        amount: subtotal
                    });
                    const url = baseUrl + '?' + params.toString();
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const j = await res.json();
                    const disc = j && j.ok ? Number(j.discount || 0) : 0;
                    elDisc.textContent = 'Rp ' + formatIDR(disc);
                    elTotal.textContent = 'Rp ' + formatIDR(Math.max(subtotal - disc, 0));
                } catch (e) {
                    elDisc.textContent = 'Rp 0';
                    elTotal.textContent = 'Rp ' + formatIDR(subtotal);
                }
            }
            window.recalc = recalc;
            if (promoInput) {
                promoInput.addEventListener('input', recalc);
                promoInput.addEventListener('change', recalc);
            }
            if (durationInput) {
                durationInput.addEventListener('input', recalc);
                durationInput.addEventListener('change', recalc);
                durationInput.addEventListener('keyup', recalc);
            }
            document.addEventListener('change', function(e) {
                const t = e.target;
                if (t && t.id === 'duration_minutes') {
                    recalc();
                }
            });
            if (lenInput) {
                lenInput.addEventListener('input', recalc);
                lenInput.addEventListener('change', recalc);
                lenInput.addEventListener('keyup', recalc);
            }
            if (widInput) {
                widInput.addEventListener('input', recalc);
                widInput.addEventListener('change', recalc);
                widInput.addEventListener('keyup', recalc);
            }
            if (qtyInput) {
                qtyInput.addEventListener('input', recalc);
                qtyInput.addEventListener('change', recalc);
                qtyInput.addEventListener('keyup', recalc);
            }
            recalc();
        })();
    </script>
            <script>
                (function(){
                    const dateInput = document.querySelector('input[name="date"]');
                    const dpSec = document.getElementById('dp_section');
                    const agree = document.getElementById('agree_dp');
                    const pmSec = document.getElementById('pm_section');
            function todayStr(){
                const d = new Date();
                const m = String(d.getMonth()+1).padStart(2,'0');
                const da = String(d.getDate()).padStart(2,'0');
                return `${d.getFullYear()}-${m}-${da}`;
            }
            function toggleDP(){
                const v = dateInput && dateInput.value || '';
                const req = Boolean(v) && v !== todayStr();
                if (dpSec) dpSec.style.display = req ? 'block' : 'none';
                if (agree) agree.required = !!req;
                const btn = document.getElementById('btn-submit');
                if (btn) {
                    const ok = !req || (agree && agree.checked);
                    btn.disabled = !ok;
                }
                if (pmSec) {
                    const showPM = !req || (agree && agree.checked);
                    pmSec.style.display = showPM ? 'block' : 'none';
                    if (showPM) {
                        const radios = pmSec.querySelectorAll('input[name="payment_method"]');
                        let anyChecked = false;
                        radios.forEach(function(r){ if (r.checked) anyChecked = true; });
                        if (!anyChecked) {
                            const cash = document.getElementById('pm_cash');
                            if (cash) cash.checked = true;
                        }
                    }
                }
            }
            document.addEventListener('DOMContentLoaded', toggleDP);
                    if (agree) {
                        agree.addEventListener('change', toggleDP);
                        agree.addEventListener('input', toggleDP);
                    }
                    if (dateInput) {
                        dateInput.addEventListener('input', toggleDP);
                        dateInput.addEventListener('change', toggleDP);
                        setTimeout(toggleDP, 0);
                    }
                })();
            </script>
</body>

</html>
