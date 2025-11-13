@extends('adminlte::page')

@section('title', 'Pengaturan')

@section('content_header')
    <h1>Pengaturan</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Umum</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.save') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}">
                            @error('company_name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Area Layanan</label>
                            <input type="text" name="service_area" class="form-control" value="{{ old('service_area', $settings['service_area'] ?? '') }}">
                            @error('service_area')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Email Notifikasi</label>
                            <input type="email" name="notify_email" class="form-control" value="{{ old('notify_email', $settings['notify_email'] ?? '') }}">
                            @error('notify_email')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="enable_notifications" id="enable_notifications" class="form-check-input" value="1" {{ old('enable_notifications', ($settings['enable_notifications'] ?? false) ? 1 : 0) ? 'checked' : '' }}>
                            <label for="enable_notifications" class="form-check-label">Aktifkan Notifikasi Email</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Preferensi Tampilan</div>
                <div class="card-body">
                    <p class="text-muted mb-2">Pengaturan tema dan tampilan AdminLTE.</p>
                    <ul class="mb-0">
                        <li>Logo: {{ config('adminlte.logo') }}</li>
                        <li>Sidebar fixed: {{ config('adminlte.layout_fixed_sidebar') ? 'Ya' : 'Tidak' }}</li>
                        <li>Dark mode: {{ config('adminlte.layout_dark_mode') ? 'Ya' : 'Tidak' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Pilihan Pembayaran</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.payment_options') }}" enctype="multipart/form-data" class="mb-4" id="paymentOptionForm">
                        @csrf
                        <input type="hidden" name="action" value="create">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="form-label">Tipe</label>
                                <select name="type" class="form-control">
                                    <option value="transfer" {{ old('type') === 'qris' ? '' : 'selected' }}>Transfer</option>
                                    <option value="qris" {{ old('type') === 'qris' ? 'selected' : '' }}>QRIS</option>
                                </select>
                                @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">Label</label>
                                <input type="text" name="label" class="form-control" placeholder="Contoh: BCA a.n ..." value="{{ old('label') }}">
                                @error('label')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group col-md-2" id="bankFields" style="display:flex; gap:12px; width:100%">
                                <div style="flex:1">
                                    <label class="form-label">Bank</label>
                                    <select name="bank_name" class="form-control">
                                        @php($banks = [
                                            'BCA','BRI','BNI','Mandiri','CIMB Niaga','Permata','BTN','Danamon',
                                            'Bank Syariah Indonesia (BSI)','Maybank Indonesia','OCBC NISP','Panin Bank',
                                            'Bank Mega','Bank Sinarmas','BTPN','Bank Jago'
                                        ])
                                        <option value="">Pilih Bank</option>
                                        @foreach ($banks as $bn)
                                            <option value="{{ $bn }}" {{ old('bank_name') === $bn ? 'selected' : '' }}>{{ $bn }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_name')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div style="flex:1">
                                    <label class="form-label">Nama Rekening</label>
                                    <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name') }}">
                                    @error('bank_account_name')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div style="flex:1">
                                    <label class="form-label">No. Rekening</label>
                                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number') }}">
                                    @error('bank_account_number')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="qrisField">
                            <label class="form-label">QRIS Image</label>
                            <input type="file" name="qris_image" class="form-control-file" accept="image/*">
                            @error('qris_image')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <button class="btn btn-success" type="submit">Tambah</button>
                    </form>
                    <script>
                        (function(){
                            var typeSelect = document.querySelector('#paymentOptionForm select[name="type"]');
                            var bankGroup = document.querySelector('#bankFields');
                            var qrisGroup = document.querySelector('#qrisField');
                            var bankName = document.querySelector('#paymentOptionForm input[name="bank_name"]');
                            var bankAccName = document.querySelector('#paymentOptionForm input[name="bank_account_name"]');
                            var bankAccNum = document.querySelector('#paymentOptionForm input[name="bank_account_number"]');
                            var qrisInput = document.querySelector('#paymentOptionForm input[name="qris_image"]');
                            function sync(){
                                var v = typeSelect.value;
                                if(v === 'qris'){
                                    bankGroup.style.display = 'none';
                                    bankName.disabled = true; bankAccName.disabled = true; bankAccNum.disabled = true;
                                    bankName.value = ''; bankAccName.value = ''; bankAccNum.value = '';
                                    qrisGroup.style.display = '';
                                    qrisInput.disabled = false;
                                }else{
                                    bankGroup.style.display = '';
                                    bankName.disabled = false; bankAccName.disabled = false; bankAccNum.disabled = false;
                                    qrisGroup.style.display = 'none';
                                    qrisInput.disabled = true;
                                    if(qrisInput) qrisInput.value = '';
                                }
                            }
                            typeSelect.addEventListener('change', sync);
                            sync();
                        })();
                    </script>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Tipe</th>
                                    <th>Detail</th>
                                    <th>Aktif</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($paymentOptions ?? []) as $opt)
                                    <tr>
                                        <td>{{ $opt['label'] ?? '-' }}</td>
                                        <td>{{ strtoupper($opt['type'] ?? '-') }}</td>
                                        <td>
                                            @if(($opt['type'] ?? '') === 'transfer')
                                                <div>Bank: {{ $opt['bank_name'] ?? '-' }}</div>
                                                <div>Nama: {{ $opt['bank_account_name'] ?? '-' }}</div>
                                                <div>No: {{ $opt['bank_account_number'] ?? '-' }}</div>
                                            @elseif(($opt['type'] ?? '') === 'qris')
                                                @if(!empty($opt['qris_image_path']))
                                                    <img src="/{{ $opt['qris_image_path'] }}" alt="QRIS" style="max-height:80px;">
                                                @else
                                                    <span class="text-muted">Belum ada gambar</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('settings.payment_options') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="{{ $opt['id'] }}">
                                                <input type="hidden" name="type" value="{{ $opt['type'] }}">
                                                <input type="hidden" name="label" value="{{ $opt['label'] }}">
                                                <input type="hidden" name="bank_name" value="{{ $opt['bank_name'] }}">
                                                <input type="hidden" name="bank_account_name" value="{{ $opt['bank_account_name'] }}">
                                                <input type="hidden" name="bank_account_number" value="{{ $opt['bank_account_number'] }}">
                                                <input type="hidden" name="active" value="{{ !empty($opt['active']) ? 0 : 1 }}">
                                                <button class="btn btn-sm btn-outline-primary" type="submit">{{ !empty($opt['active']) ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                            </form>
                                        </td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('settings.payment_options') }}" class="d-inline" onsubmit="return confirm('Hapus pilihan ini?')">
                                                @csrf
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="{{ $opt['id'] }}">
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada pilihan pembayaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
