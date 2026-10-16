<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $invoice->no_invoice }}</h6>
        <a class="btn btn-sm btn-outline-secondary float-end opname-no-print" href="{{ route('opname_menu.bulanan', ['bulan' => substr($invoice->bulan, 0, 7)]) }}">Daftar invoice</a>
    </x-slot>
    <x-slot name="cardBody">
        <div id="opname-page-content">
        @if (session('sukses'))<div class="alert alert-success opname-no-print">{{ session('sukses') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger opname-no-print"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @if ($invoice->alasan_kembali)<div class="alert alert-warning">Dikembalikan pengawas: {{ $invoice->alasan_kembali }}</div>@endif
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 opname-head">
            <div class="opname-head-info"><span class="badge bg-{{ $invoice->status === 'locked' ? 'success' : ($invoice->status === 'submitted' ? 'warning text-dark' : 'secondary') }}">{{ ['draft' => 'Draft', 'submitted' => 'Diajukan', 'locked' => 'Terkunci'][$invoice->status] }}</span><strong class="ms-2">{{ $positions[$invoice->posisi] }}</strong><span class="ms-2">Pengawas: {{ $invoice->pengawas?->name }}</span></div>
            <div class="d-flex flex-wrap gap-2 opname-no-print opname-head-actions">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#opname-sisa-modal">Sisa box belum diopname</button>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('opname_bulanan.print', $invoice->id) }}" target="_blank" rel="noopener">Cetak lembar PO opname</a>
            </div>
        </div>
        <div class="row g-2 g-md-3 mb-4 opname-summary">
            <div class="col-6 col-md-4"><div class="border rounded p-2 p-md-3 h-100"><small class="text-muted">Periode / tanggal</small><div>{{ \Carbon\Carbon::parse($invoice->bulan)->format('m-Y') }} / {{ \Carbon\Carbon::parse($invoice->tanggal)->format('d-m-Y') }}</div><small class="text-muted">Dibuat oleh {{ $invoice->pembuat?->name }}</small></div></div>
            <div class="col-6 col-md-4"><div class="border rounded p-2 p-md-3 h-100"><small class="text-muted">Partai / tujuan</small><div class="fw-bold">{{ $invoice->nm_partai ?? '—' }} / {{ $invoice->tujuan }}</div>@if ($invoice->no_pengiriman)<span>Pengiriman #{{ $invoice->no_pengiriman }}</span>@else<small>Satu pengiriman setelah invoice dikunci.</small>@endif</div></div>
            <div class="col-12 col-md-4"><div class="border rounded p-2 p-md-3 h-100"><small class="text-muted">Hasil hitung</small><div class="fw-bold">{{ $invoice->lines->count() }} box · {{ number_format($invoice->lines->sum('pcs_real')) }} pcs · {{ number_format($invoice->lines->sum('gr_real')) }} gr</div>@if ($invoice->locked_at)<small>Dikunci {{ $invoice->locked_at->format('d-m-Y H:i') }}</small>@endif</div></div>
        </div>
        @if ($editable)
            <details class="mb-3 opname-no-print"><summary>Ubah nama partai, tanggal, tujuan, dan catatan</summary><form class="row g-2 mt-1" method="POST" action="{{ route('opname_bulanan.update', $invoice->id) }}" data-opname-ajax-save>@csrf @method('PATCH')<div class="col-md-2"><label for="invoice-tgl" class="form-label">Tanggal</label><input id="invoice-tgl" type="date" class="form-control" name="tanggal" value="{{ $invoice->tanggal }}" required></div><div class="col-md-3"><label for="invoice-partai" class="form-label">Nama partai</label><input id="invoice-partai" class="form-control" name="nm_partai" value="{{ old('nm_partai', $invoice->nm_partai) }}" maxlength="100" required></div><div class="col-md-2"><label for="invoice-tujuan" class="form-label">Tujuan</label><input id="invoice-tujuan" class="form-control" name="tujuan" value="{{ $invoice->tujuan }}" maxlength="100" required></div><div class="col-md-3"><label for="invoice-catatan" class="form-label">Catatan</label><input id="invoice-catatan" class="form-control" name="catatan" value="{{ $invoice->catatan }}" maxlength="2000"></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary w-100">Simpan</button></div></form></details>
            <div class="card border-primary mb-4 opname-no-print">
                <div class="card-header"><h6 class="mb-0">Tambah baris box</h6></div>
                <div class="card-body">
                    @php
                        $lineCount = $invoice->lines->count();
                        $lastBaris = $invoice->lines->last()?->baris;
                        $nextBaris = '1-1';
                        $barisGroup = 1;
                        $lastSequence = 0;
                        if ($lastBaris && preg_match('/^([1-9][0-9]{0,3})-([1-9][0-9]{0,3})$/', $lastBaris, $barisParts)) {
                            $barisGroup = (int) $barisParts[1];
                            $lastSequence = (int) $barisParts[2];
                            $nextBaris = $barisGroup.'-'.($lastSequence + 1);
                        }
                    @endphp
                    @if ($lastSequence > 0 && $lastSequence % 20 === 0)
                        <div class="alert alert-warning fw-semibold" role="alert" aria-live="assertive">
                            Urutan {{ $lastSequence - 19 }}–{{ $lastSequence }} pada kelompok {{ $barisGroup }} sudah lengkap. Input berikutnya akan masuk ke baris {{ $nextBaris }}.
                        </div>
                    @endif
                    <form action="{{ route('opname_bulanan.line', $invoice->id) }}" method="POST" id="opname-add-form" class="row g-3" data-opname-ajax-save>
                        @csrf
                        <div class="col-4 col-md-3"><label class="form-label" for="opname-baris">Baris</label><input id="opname-baris" name="baris" class="form-control" placeholder="1-1" pattern="[1-9][0-9]{0,3}-[1-9][0-9]{0,3}" value="{{ old('line_id') ? $nextBaris : old('baris', $nextBaris) }}" required></div>
                        <div class="col-8 col-md-9"><label class="form-label" for="opname-box">Box lama</label><input id="opname-box" name="no_box" class="form-control" value="{{ old('line_id') ? '' : old('no_box') }}" placeholder="Ketik nomor box" inputmode="numeric" pattern="[1-9][0-9]{0,8}" autocomplete="off" required></div>
                        <div class="col-12" id="opname-source-details" hidden>
                            <details class="border rounded bg-light px-3 py-2">
                                <summary class="fw-semibold">Rincian box asal — ketuk untuk lihat</summary>
                                <dl class="row g-3 mb-0 mt-2">
                                    <div class="col-6 col-md-2"><dt class="small text-muted fw-normal">No. box</dt><dd id="opname-source-box" class="fw-bold mb-0"></dd></div>
                                    <div class="col-6 col-md-4"><dt class="small text-muted fw-normal">Partai</dt><dd id="opname-source-partai" class="fw-bold mb-0"></dd></div>
                                    <div class="col-4 col-md-2"><dt class="small text-muted fw-normal">Grade asal</dt><dd id="opname-source-grade" class="fw-bold mb-0"></dd></div>
                                    <div class="col-4 col-md-2"><dt class="small text-muted fw-normal">Pcs sistem</dt><dd id="opname-source-pcs" class="fw-bold mb-0"></dd></div>
                                    <div class="col-4 col-md-2"><dt class="small text-muted fw-normal">Gram sistem</dt><dd id="opname-source-gr" class="fw-bold mb-0"></dd></div>
                                </dl>
                            </details>
                        </div>
                        <div class="col-12 col-md-3"><label class="form-label" for="opname-grade">Grade real</label><input id="opname-grade" name="grade_real" class="form-control" maxlength="50" value="{{ old('line_id') ? '' : old('grade_real') }}" required></div>
                        <div class="col-6 col-md-3"><label class="form-label" for="opname-pcs">Pcs real</label><input id="opname-pcs" name="pcs_real" type="number" min="0" step="1" class="form-control" value="{{ old('line_id') ? '' : old('pcs_real') }}" required></div>
                        <div class="col-6 col-md-3"><label class="form-label" for="opname-gr">Gram real</label><input id="opname-gr" name="gr_real" type="number" min="0.01" step="0.01" class="form-control" value="{{ old('line_id') ? '' : old('gr_real') }}" required></div>
                        <div class="col-md-3 d-flex align-items-end"><button id="opname-save" class="btn btn-primary w-100">Simpan</button></div>
                        <div class="col-12"><div class="alert alert-secondary mb-0 fw-semibold" id="opname-source-info" role="alert" aria-live="assertive">Ketik nomor box. Data box akan diambil otomatis.</div></div>
                    </form>
                </div>
            </div>
        @endif
        @if ($invoice->catatan)<p>Catatan: {{ $invoice->catatan }}</p>@endif
        <p class="small text-muted">Stok sistem dan biaya diambil saat baris disimpan. Selisih = real − sistem. Nomor baris mengikuti lembar hitung, misalnya 1-1, 1-2.</p>
        <div class="table-responsive opname-lines-wrapper">
            <table class="table table-sm table-bordered align-middle opname-lines">
                <thead><tr><th>Baris</th><th>Box lama</th><th>Partai</th><th>Grade sistem</th><th>Grade real</th><th class="text-end">Pcs sistem</th><th class="text-end">Pcs real</th><th class="text-end">Δ Pcs</th><th class="text-end">Gr sistem</th><th class="text-end">Gr real</th><th class="text-end">Δ Gr</th><th>Box baru<br><small>Posisi asal</small></th><th>Box kirim</th>@if ($editable)<th class="opname-no-print">Aksi</th>@endif</tr></thead>
                <tbody>
                    @forelse ($invoice->lines as $line)
                        <tr class="opname-line-row">
                            <td data-label="Baris">{{ $line->baris }}</td>
                            <td data-label="Box lama" class="fw-bold"><span class="opname-box-no">{{ $line->no_box }}</span><button type="button" class="opname-mobile-card-toggle opname-no-print" data-opname-detail-toggle aria-expanded="false" aria-label="Tampilkan detail box {{ $line->no_box }}" data-box="{{ $line->no_box }}"><span aria-hidden="true">›</span></button></td>
                            <td class="opname-mobile-metrics">
                                <div class="opname-mobile-metric">
                                    <span class="opname-mobile-metric-label">Grade</span>
                                    <strong class="opname-mobile-metric-value {{ $line->grade_sistem !== $line->grade_real ? 'text-warning' : '' }}">{{ $line->grade_real }}</strong>
                                </div>
                                <div class="opname-mobile-metric">
                                    <span class="opname-mobile-metric-label">Pcs</span>
                                    <div class="opname-mobile-metric-value"><strong>{{ number_format($line->pcs_real) }}</strong> <span class="opname-mobile-delta {{ $line->pcs_real != $line->pcs_sistem ? 'text-danger fw-bold' : 'text-muted' }}">{{ ($line->pcs_real - $line->pcs_sistem) > 0 ? '+' : '' }}{{ number_format($line->pcs_real - $line->pcs_sistem) }}</span></div>
                                </div>
                                <div class="opname-mobile-metric">
                                    <span class="opname-mobile-metric-label">Gram</span>
                                    <div class="opname-mobile-metric-value"><strong>{{ number_format($line->gr_real) }}</strong> <span class="opname-mobile-delta {{ abs($line->gr_real - $line->gr_sistem) >= 0.01 ? 'text-danger fw-bold' : 'text-muted' }}">{{ ($line->gr_real - $line->gr_sistem) > 0 ? '+' : '' }}{{ number_format($line->gr_real - $line->gr_sistem) }}</span></div>
                                </div>
                            </td>
                            <td data-label="Partai" class="opname-mobile-detail">{{ $line->nm_partai }}</td>
                            <td data-label="Grade sistem" class="opname-mobile-detail">{{ $line->grade_sistem }}</td>
                            <td data-label="Grade real" class="opname-desktop-primary {{ $line->grade_sistem !== $line->grade_real ? 'text-warning fw-bold' : '' }}">{{ $line->grade_real }}</td>
                            <td data-label="Pcs sistem" class="text-end opname-mobile-detail">{{ number_format($line->pcs_sistem) }}</td>
                            <td data-label="Pcs real" class="text-end fw-bold opname-desktop-primary">{{ number_format($line->pcs_real) }}</td>
                            <td data-label="Selisih pcs" class="text-end opname-delta opname-desktop-primary {{ $line->pcs_real != $line->pcs_sistem ? 'text-danger fw-bold' : 'opname-delta-zero' }}">{{ ($line->pcs_real - $line->pcs_sistem) > 0 ? '+' : '' }}{{ number_format($line->pcs_real - $line->pcs_sistem) }}</td>
                            <td data-label="Gram sistem" class="text-end opname-mobile-detail">{{ number_format($line->gr_sistem) }}</td>
                            <td data-label="Gram real" class="text-end fw-bold opname-desktop-primary">{{ number_format($line->gr_real) }}</td>
                            <td data-label="Selisih gram" class="text-end opname-delta opname-desktop-primary {{ abs($line->gr_real - $line->gr_sistem) >= 0.01 ? 'text-danger fw-bold' : 'opname-delta-zero' }}">{{ ($line->gr_real - $line->gr_sistem) > 0 ? '+' : '' }}{{ number_format($line->gr_real - $line->gr_sistem) }}</td>
                            <td data-label="Box baru" class="opname-mobile-detail">{{ $line->no_box_baru ?? 'Setelah dikunci' }}</td>
                            <td data-label="Box kirim" class="opname-mobile-detail">{{ $line->no_box_kirim ?? '—' }}</td>
                            @if ($editable)<td class="opname-no-print opname-line-actions"><div class="d-flex gap-2"><button class="btn btn-sm btn-outline-secondary opname-action-detail" type="button" data-opname-detail-toggle aria-expanded="false" aria-label="Tampilkan detail box {{ $line->no_box }}" data-box="{{ $line->no_box }}">Detail</button><button class="btn btn-sm btn-outline-primary" type="button" onclick="document.getElementById('edit-line-{{ $line->id }}').classList.toggle('d-none')">Edit</button><form action="{{ route('opname_bulanan.line.delete', [$invoice->id, $line->id]) }}" method="POST" onsubmit="return confirm('Hapus baris {{ $line->baris }} dari draft?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger w-100">Hapus</button></form></div></td>@endif
                        </tr>
                        @if ($editable)
                            <tr id="edit-line-{{ $line->id }}" class="{{ (int) old('line_id') === (int) $line->id ? '' : 'd-none' }} opname-no-print opname-line-edit"><td colspan="14"><form action="{{ route('opname_bulanan.line', $invoice->id) }}" method="POST" class="row g-2" data-opname-ajax-save>@csrf<input type="hidden" name="line_id" value="{{ $line->id }}"><input type="hidden" name="no_box" value="{{ $line->no_box }}"><div class="col-6 col-md-2"><label class="form-label" for="baris-{{ $line->id }}">Baris</label><input id="baris-{{ $line->id }}" class="form-control" name="baris" value="{{ $line->baris }}" pattern="[1-9][0-9]{0,3}-[1-9][0-9]{0,3}" required></div><div class="col-6 col-md-3"><label class="form-label" for="grade-{{ $line->id }}">Grade real</label><input id="grade-{{ $line->id }}" class="form-control" name="grade_real" value="{{ $line->grade_real }}" maxlength="50" required></div><div class="col-6 col-md-2"><label class="form-label" for="pcs-{{ $line->id }}">Pcs real</label><input id="pcs-{{ $line->id }}" class="form-control" name="pcs_real" type="number" min="0" step="1" value="{{ $line->pcs_real }}" required></div><div class="col-6 col-md-2"><label class="form-label" for="gr-{{ $line->id }}">Gram real</label><input id="gr-{{ $line->id }}" class="form-control" name="gr_real" type="number" min="0.01" step="0.01" value="{{ $line->gr_real }}" required></div><div class="col-md-3 d-grid align-items-end"><button class="btn btn-outline-primary">Simpan perubahan</button></div></form></td></tr>
                        @endif
                    @empty
                        <tr><td colspan="{{ $editable ? 14 : 13 }}" class="text-center text-muted py-4">Belum ada box dalam invoice.</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr class="fw-bold"><td colspan="5">Total</td><td class="text-end">{{ number_format($invoice->lines->sum('pcs_sistem')) }}</td><td class="text-end">{{ number_format($invoice->lines->sum('pcs_real')) }}</td><td class="text-end">{{ number_format($invoice->lines->sum('pcs_real') - $invoice->lines->sum('pcs_sistem')) }}</td><td class="text-end">{{ number_format($invoice->lines->sum('gr_sistem')) }}</td><td class="text-end">{{ number_format($invoice->lines->sum('gr_real')) }}</td><td class="text-end">{{ number_format($invoice->lines->sum('gr_real') - $invoice->lines->sum('gr_sistem')) }}</td><td colspan="{{ $editable ? 3 : 2 }}"></td></tr></tfoot>
            </table>
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mt-2 opname-no-print opname-pager" id="opname-pager" hidden>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="opname-prev">‹ Sebelumnya</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="opname-next">Berikutnya ›</button>
                </div>
                <small class="text-muted" id="opname-page-info"></small>
                <label class="d-flex gap-1 align-items-center small text-muted mb-0">Tampil
                    <select id="opname-perpage" class="form-select form-select-sm" style="width:auto">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
            </div>
        </div>
        @if ($canSeeValue && $invoice->status === 'locked' && $invoice->lines->isNotEmpty())
            <h6 class="mt-4">Modal awal box baru</h6>
            <div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Box lama → baru</th><th class="text-end">BK asal</th><th class="text-end">Kerja</th><th class="text-end">OP</th><th class="text-end">CU</th><th class="text-end">BK baru</th><th class="text-end">Rp / gr baru</th></tr></thead><tbody>@foreach ($invoice->lines as $line)<tr><td>{{ $line->no_box }} → {{ $line->no_box_baru ?? 'Belum dibuat' }}</td>@foreach (['cost_bk', 'cost_kerja', 'cost_op', 'cost_cu', 'modal_baru', 'harga_baru'] as $field)<td class="text-end">{{ number_format($line->{$field}, 2) }}</td>@endforeach</tr>@endforeach</tbody><tfoot><tr class="fw-bold"><td>Total</td>@foreach (['cost_bk', 'cost_kerja', 'cost_op', 'cost_cu', 'modal_baru'] as $field)<td class="text-end">{{ number_format($invoice->lines->sum($field), 2) }}</td>@endforeach<td></td></tr></tfoot></table></div>
            <p class="small text-muted">BK baru = BK asal + kerja + OP + CU. Biaya kerja / OP box baru mulai dari nol. Biaya box lama dibekukan ketika invoice dikunci.</p>
        @endif
        <div class="mt-4 opname-no-print opname-action-bar">
            @if ($editable && $invoice->lines->isNotEmpty())
                <div class="mb-2"><strong>Langkah terakhir</strong><div class="small text-muted">Pastikan semua box sudah disimpan. Tombol ini hanya ditekan sekali di akhir.</div></div>
                <form method="POST" class="mt-2" action="{{ route('opname_bulanan.submit', $invoice->id) }}" onsubmit="return confirm('Ajukan seluruh baris kepada pengawas? Invoice berhenti dapat diedit sampai dikembalikan.')">@csrf<button class="btn btn-primary">Ajukan ke {{ $invoice->pengawas?->name }}</button></form>
            @elseif ($invoice->status === 'submitted' && $approver)
                <div class="alert alert-info">Penguncian meneruskan seluruh box lama sampai pengiriman ke <strong>{{ $invoice->tujuan }}</strong>, lalu membuat box baru dengan jumlah real yang sama di <strong>{{ $positions[$invoice->posisi] }}</strong>.</div>
                <form action="{{ route('opname_bulanan.lock', $invoice->id) }}" method="POST" class="mb-3" onsubmit="return confirm('Setujui dan kunci invoice? Pengiriman dan box baru akan dibuat. Invoice tidak dapat diedit lagi.')">@csrf<button class="btn btn-success">Setujui &amp; kunci invoice</button></form>
                <form action="{{ route('opname_bulanan.return', $invoice->id) }}" method="POST" class="row g-2">@csrf<div class="col-md-8"><label for="return-reason" class="form-label">Alasan koreksi</label><input id="return-reason" name="alasan" class="form-control" maxlength="2000" required></div><div class="col-md-4 d-flex align-items-end"><button class="btn btn-outline-warning">Kembalikan ke pembuat</button></div></form>
            @elseif ($invoice->status === 'submitted')
                <div class="alert alert-info">Menunggu persetujuan {{ $invoice->pengawas?->name }}.</div>
            @elseif ($invoice->status === 'locked')
                <div class="alert alert-success">Invoice terkunci. Pengiriman #{{ $invoice->no_pengiriman }} ke {{ $invoice->tujuan }} sudah tercatat. Hubungan box lama, box baru, dan box kirim tersimpan di atas.</div>
                <a class="btn btn-outline-primary" href="{{ route('pengiriman.print_formulir_grade', ['no_invoice' => $invoice->no_pengiriman]) }}" target="_blank" rel="noopener">Cetak pengiriman #{{ $invoice->no_pengiriman }}</a>
            @endif
        </div>
        </div>
        <div class="modal fade opname-no-print" id="opname-sisa-modal" tabindex="-1" aria-labelledby="opname-sisa-title" aria-hidden="true" data-url="{{ route('opname_menu.bulanan.belum_opname', ['bulan' => substr($invoice->bulan, 0, 7), 'posisi' => $invoice->posisi, 'pengawas_id' => $invoice->pengawas_id]) }}">
            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="opname-sisa-title">Sisa box belum diopname</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <small class="text-muted">{{ $positions[$invoice->posisi] }} • {{ $invoice->pengawas?->name }} • {{ \Carbon\Carbon::parse($invoice->bulan)->format('m-Y') }}</small>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" id="opname-sisa-refresh">Muat ulang</button>
                        </div>
                        <div class="alert alert-info py-2 small" id="opname-sisa-total" hidden></div>
                        <div class="alert alert-secondary py-2 small mb-2" id="opname-sisa-info" role="status">Membuka daftar…</div>
                        <div class="list-group list-group-flush" id="opname-sisa-list"></div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('opname_menu.bulanan.belum_opname', ['bulan' => substr($invoice->bulan, 0, 7), 'posisi' => $invoice->posisi, 'pengawas_id' => $invoice->pengawas_id]) }}">Buka halaman penuh</a>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @if ($editable)
            <script>
                const initOpnamePage = () => {
                    const box = document.getElementById('opname-box');
                    const info = document.getElementById('opname-source-info');
                    const details = document.getElementById('opname-source-details');
                    const gradeReal = document.getElementById('opname-grade');
                    const pcsReal = document.getElementById('opname-pcs');
                    const gramReal = document.getElementById('opname-gr');
                    const addForm = document.getElementById('opname-add-form');
                    const saveButton = document.getElementById('opname-save');
                    if (!box || !info || !details || !pcsReal || !gramReal) return;
                    const pcsFormat = new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2});
                    const gramFormat = new Intl.NumberFormat('id-ID', {maximumFractionDigits: 0});
                    let lookupVersion = 0;
                    let lookupState = 'empty';
                    let foundBox = '';
                    const setFormReady = (ready) => {
                        if (saveButton) saveButton.disabled = !ready;
                        [gradeReal, pcsReal, gramReal].forEach((el) => { if (el) el.disabled = !ready; });
                    };
                    const showLookupMessage = (message, type = 'secondary', searching = false) => {
                        info.className = `alert alert-${type} mb-0 fw-semibold`;
                        info.textContent = '';
                        if (searching) {
                            const spinner = document.createElement('span');
                            spinner.className = 'spinner-border spinner-border-sm me-2';
                            spinner.setAttribute('aria-hidden', 'true');
                            info.appendChild(spinner);
                        }
                        info.appendChild(document.createTextNode(message));
                    };
                    const setLookupState = (state, message, type = 'secondary', searching = false) => {
                        lookupState = state;
                        showLookupMessage(message, type, searching);
                    };
                    const clearSourceData = () => {
                        ['opname-source-box', 'opname-source-partai', 'opname-source-grade', 'opname-source-pcs', 'opname-source-gr']
                            .forEach((id) => { const el = document.getElementById(id); if (el) el.textContent = ''; });
                        if (gradeReal) gradeReal.value = '';
                        pcsReal.value = '';
                        gramReal.value = '';
                        details.hidden = true;
                    };
                    const invalidateFound = () => {
                        if (box.value.trim() === foundBox && foundBox !== '') return;
                        foundBox = '';
                        details.hidden = true;
                        setFormReady(false);
                    };
                    const loadSelectedBox = async () => {
                        const version = ++lookupVersion;
                        const number = box.value.trim();
                        invalidateFound();
                        details.hidden = true;
                        if (!number) {
                            clearSourceData();
                            setLookupState('empty', 'Ketik nomor box. Data box akan diambil otomatis.');
                            setFormReady(false);
                            return;
                        }
                        if (!/^[1-9][0-9]{0,8}$/.test(number) || number === '9999') {
                            clearSourceData();
                            setLookupState('rejected', 'Nomor box tidak valid. Gunakan 1–999999999 selain 9999, lalu ketik ulang.', 'warning');
                            setFormReady(false);
                            return;
                        }
                        setLookupState('searching', `Mencari box ${number}…`, 'info', true);
                        setFormReady(false);
                        try {
                            const url = new URL(@json(route('opname_bulanan.source', $invoice->id)), window.location.origin);
                            url.searchParams.set('no_box', number);
                            const response = await fetch(url, {headers: {'Accept': 'application/json'}, credentials: 'same-origin'});
                            const data = await response.json();
                            if (version !== lookupVersion) return;
                            if (box.value.trim() !== number) return;
                            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'Box tidak dapat diambil.');
                            document.getElementById('opname-source-box').textContent = data.no_box;
                            document.getElementById('opname-source-partai').textContent = data.nm_partai || '—';
                            document.getElementById('opname-source-grade').textContent = data.grade || '—';
                            document.getElementById('opname-source-pcs').textContent = `${pcsFormat.format(data.pcs)} pcs`;
                            document.getElementById('opname-source-gr').textContent = `${gramFormat.format(data.gr)} gr`;
                            details.hidden = false;
                            gradeReal.value = data.grade || '';
                            pcsReal.value = data.pcs ?? '';
                            gramReal.value = data.gr ?? '';
                            foundBox = number;
                            setLookupState('found', `Box ${data.no_box} ditemukan. Pcs dan gram real sudah diisi dari stok sistem. Ubah nilainya jika hasil hitung fisik berbeda.`, 'success');
                            setFormReady(true);
                        } catch (error) {
                            if (version !== lookupVersion) return;
                            if (box.value.trim() !== number) return;
                            clearSourceData();
                            foundBox = '';
                            setLookupState('rejected', error.message, 'danger');
                            setFormReady(false);
                        }
                    };
                    let lookupTimer;
                    const queueBoxLookup = () => {
                        window.clearTimeout(lookupTimer);
                        lookupVersion++;
                        invalidateFound();
                        details.hidden = true;
                        const number = box.value.trim();
                        if (!number) {
                            clearSourceData();
                            setLookupState('empty', 'Ketik nomor box. Data box akan diambil otomatis.');
                            setFormReady(false);
                            return;
                        }
                        if (!/^[1-9][0-9]{0,8}$/.test(number) || number === '9999') {
                            clearSourceData();
                            setLookupState('rejected', 'Selesaikan nomor box yang valid.', 'warning');
                            setFormReady(false);
                            return;
                        }
                        setLookupState('waiting', 'Menunggu nomor box selesai diketik…', 'info');
                        setFormReady(false);
                        lookupTimer = window.setTimeout(loadSelectedBox, 450);
                    };
                    box.addEventListener('input', queueBoxLookup);
                    box.addEventListener('keydown', event => {
                        if (event.key !== 'Enter') return;
                        event.preventDefault();
                        window.clearTimeout(lookupTimer);
                        loadSelectedBox();
                    });
                    [gradeReal, pcsReal, gramReal].forEach((el) => {
                        el?.addEventListener('keydown', (event) => {
                            if (event.key !== 'Enter') return;
                            event.preventDefault();
                            if (lookupState !== 'found' || box.value.trim() !== foundBox || !foundBox) {
                                window.clearTimeout(lookupTimer);
                                loadSelectedBox();
                                return;
                            }
                            if (el === gradeReal) pcsReal.focus();
                            else if (el === pcsReal) gramReal.focus();
                            else addForm?.requestSubmit(saveButton);
                        });
                    });

                    document.querySelectorAll('[data-opname-ajax-save]').forEach(form => {
                        form.addEventListener('submit', async event => {
                            if (form.id === 'opname-add-form' && (lookupState !== 'found' || box.value.trim() !== foundBox || !foundBox)) {
                                event.preventDefault();
                                window.clearTimeout(lookupTimer);
                                if (box.value.trim()) {
                                    setLookupState('rejected', 'Pilih box yang sudah ditemukan sebelum menyimpan. Lookup dijalankan ulang.', 'warning');
                                    loadSelectedBox();
                                } else {
                                    setLookupState('empty', 'Ketik nomor box. Data box akan diambil otomatis.');
                                }
                                box.focus();
                                return;
                            }
                            event.preventDefault();
                            const focusNextBox = form.id === 'opname-add-form';
                            const submit = form.querySelector('button[type="submit"], button:not([type])');
                            const originalText = submit?.textContent;
                            if (submit) {
                                submit.disabled = true;
                                submit.textContent = 'Menyimpan…';
                            }
                            try {
                                const response = await fetch(form.action, {
                                    method: form.method,
                                    body: new FormData(form),
                                    headers: {'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest'},
                                    credentials: 'same-origin',
                                });
                                const html = await response.text();
                                const replacement = new DOMParser().parseFromString(html, 'text/html')
                                    .getElementById('opname-page-content');
                                if (!response.ok || !replacement) throw new Error('Data belum dapat disimpan. Silakan coba lagi.');
                                document.getElementById('opname-page-content').replaceWith(replacement);
                                if (focusNextBox) window._opnamePage = 'last';
                                initOpnamePage();
                                if (window.initOpnameLinesPaging) window.initOpnameLinesPaging();
                                if (focusNextBox) {
                                    const nextBox = document.getElementById('opname-box');
                                    nextBox?.focus();
                                }
                            } catch (error) {
                                const currentInfo = document.getElementById('opname-source-info');
                                if (currentInfo) {
                                    currentInfo.className = 'alert alert-danger mb-0 fw-semibold';
                                    currentInfo.textContent = error.message;
                                }
                                if (submit?.isConnected) {
                                    submit.disabled = false;
                                    submit.textContent = originalText;
                                }
                            }
                        });
                    });
                    if (box.value.trim()) {
                        loadSelectedBox();
                    } else {
                        clearSourceData();
                        setLookupState('empty', 'Ketik nomor box. Data box akan diambil otomatis.');
                        setFormReady(false);
                    }
                };
                document.addEventListener('DOMContentLoaded', initOpnamePage);
            </script>
        @endif
        <script>
            document.addEventListener('click', event => {
                const button = event.target.closest('[data-opname-detail-toggle]');
                if (!button) return;
                const row = button.closest('.opname-line-row');
                if (!row) return;
                const expanded = row.classList.toggle('opname-mobile-expanded');
                row.querySelectorAll('[data-opname-detail-toggle]').forEach((toggle) => {
                    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                    const box = toggle.getAttribute('data-box') || '';
                    toggle.setAttribute('aria-label', (expanded ? 'Sembunyikan detail box ' : 'Tampilkan detail box ') + box);
                    if (toggle.classList.contains('opname-action-detail')) {
                        toggle.textContent = expanded ? 'Tutup' : 'Detail';
                    }
                });
            });
            window.initOpnameLinesPaging = () => {
                const pager = document.getElementById('opname-pager');
                const prev = document.getElementById('opname-prev');
                const next = document.getElementById('opname-next');
                const info = document.getElementById('opname-page-info');
                const perpage = document.getElementById('opname-perpage');
                if (!pager || !prev || !next || !info || !perpage) return;
                const rows = [...document.querySelectorAll('.opname-lines .opname-line-row')];
                if (!rows.length) { pager.hidden = true; return; }
                if (window._opnamePerPage) perpage.value = window._opnamePerPage;
                const per = parseInt(perpage.value, 10) || 20;
                window._opnamePerPage = per;
                const total = Math.max(1, Math.ceil(rows.length / per));
                let page = window._opnamePage || 1;
                if (page === 'last') page = total;
                if (typeof page !== 'number' || isNaN(page)) page = 1;
                page = Math.min(Math.max(1, page), total);
                window._opnamePage = page;
                pager.hidden = rows.length <= per;
                const start = (page - 1) * per;
                rows.forEach((row, i) => {
                    const show = i >= start && i < start + per;
                    row.classList.toggle('opname-pg-hide', !show);
                    const edit = row.nextElementSibling;
                    if (edit && edit.classList && edit.classList.contains('opname-line-edit')) {
                        edit.classList.toggle('opname-pg-hide', !show);
                    }
                });
                const end = Math.min(start + per, rows.length);
                info.textContent = 'Hal ' + page + ' dari ' + total + ' • ' + (start + 1) + '–' + end + ' dari ' + rows.length + ' box';
                prev.disabled = page <= 1;
                next.disabled = page >= total;
                if (!pager.dataset.bound) {
                    pager.dataset.bound = '1';
                    prev.addEventListener('click', () => {
                        window._opnamePage = (window._opnamePage || 1) - 1;
                        window.initOpnameLinesPaging();
                        document.querySelector('.opname-lines-wrapper')?.scrollIntoView({block: 'nearest'});
                    });
                    next.addEventListener('click', () => {
                        window._opnamePage = (window._opnamePage || 1) + 1;
                        window.initOpnameLinesPaging();
                        document.querySelector('.opname-lines-wrapper')?.scrollIntoView({block: 'nearest'});
                    });
                    perpage.addEventListener('change', () => {
                        window._opnamePerPage = parseInt(perpage.value, 10) || 20;
                        window._opnamePage = 1;
                        window.initOpnameLinesPaging();
                    });
                }
            };
            document.addEventListener('DOMContentLoaded', () => window.initOpnameLinesPaging());
            (() => {
                const modal = document.getElementById('opname-sisa-modal');
                if (!modal || modal.dataset.bound) return;
                modal.dataset.bound = '1';
                const list = document.getElementById('opname-sisa-list');
                const info = document.getElementById('opname-sisa-info');
                const total = document.getElementById('opname-sisa-total');
                const refresh = document.getElementById('opname-sisa-refresh');
                const url = modal.dataset.url;
                let loaded = false;
                const load = async () => {
                    info.hidden = false;
                    info.className = 'alert alert-secondary py-2 small mb-2';
                    info.textContent = 'Mengambil sisa box…';
                    list.innerHTML = '';
                    total.hidden = true;
                    try {
                        const response = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'});
                        const html = await response.text();
                        if (!response.ok) throw new Error('Daftar belum dapat diambil.');
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const sums = [...doc.querySelectorAll('.fs-4.fw-bold')].map(el => el.textContent.trim());
                        if (sums.length >= 3) {
                            total.hidden = false;
                            total.textContent = sums[0] + ' box • ' + sums[1] + ' pcs • ' + sums[2] + ' gr belum diopname';
                        }
                        const rows = [...doc.querySelectorAll('table.table tbody tr')];
                        const boxInput = document.getElementById('opname-box');
                        let count = 0;
                        rows.forEach(row => {
                            const cells = [...row.querySelectorAll('td')].map(td => td.textContent.trim());
                            if (cells.length < 7) return;
                            count++;
                            const item = document.createElement(boxInput ? 'button' : 'div');
                            item.type = boxInput ? 'button' : undefined;
                            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-2 text-start';
                            const left = document.createElement('div');
                            const strong = document.createElement('strong');
                            strong.textContent = cells[0];
                            const small = document.createElement('small');
                            small.className = 'd-block text-muted';
                            small.textContent = (cells[1] || '—') + ' • ' + (cells[4] || '—');
                            left.appendChild(strong);
                            left.appendChild(small);
                            const right = document.createElement('div');
                            right.className = 'text-end text-nowrap';
                            right.innerHTML = '<strong>' + cells[5] + ' pcs</strong><br><small class="text-muted">' + cells[6] + ' gr</small>';
                            item.appendChild(left);
                            item.appendChild(right);
                            if (boxInput) {
                                item.addEventListener('click', () => {
                                    boxInput.value = cells[0];
                                    boxInput.dispatchEvent(new Event('input', {bubbles: true}));
                                    boxInput.focus();
                                    window.bootstrap?.Modal.getInstance(modal)?.hide();
                                });
                            }
                            list.appendChild(item);
                        });
                        info.hidden = true;
                        if (!count) {
                            info.hidden = false;
                            info.className = 'alert alert-success py-2 small mb-2';
                            info.textContent = 'Semua box posisi ini sudah diopname.';
                        } else if (boxInput) {
                            info.hidden = false;
                            info.className = 'alert alert-secondary py-2 small mb-2';
                            info.textContent = 'Ketuk box untuk mengisinya ke form tambah.';
                        }
                        loaded = true;
                    } catch (error) {
                        info.className = 'alert alert-danger py-2 small mb-2';
                        info.textContent = 'Daftar belum dapat diambil. Gunakan tombol Buka halaman penuh.';
                    }
                };
                modal.addEventListener('show.bs.modal', () => { if (!loaded) load(); });
                refresh?.addEventListener('click', () => { loaded = false; load(); });
            })();
        </script>
        <style>
            .opname-lines th, .opname-lines td { white-space: nowrap; }
            .opname-mobile-card-toggle { display: none; }
            .opname-action-detail { display: none; }
            .opname-mobile-metrics { display: none; }
            .opname-lines .opname-pg-hide { display: none !important; }
            .opname-pager .btn { min-height: 40px; }
            .opname-pager .form-select { min-height: 40px; }
            #opname-sisa-list .list-group-item { min-height: 56px; padding: .65rem .9rem; }
            @media (max-width: 767.98px) {
                .opname-head-actions { flex-wrap: nowrap; }
                .opname-head-actions .btn { flex: 1 1 0; white-space: normal; }
            }
            @media (max-width: 767.98px) {
                .opname-head-info { font-size: .85rem; overflow-wrap: anywhere; }
                #opname-add-form { row-gap: .75rem !important; }
                #opname-add-form .form-control,
                #opname-add-form .form-select { min-height: 44px; font-size: 16px; }
                #opname-add-form .btn { min-height: 48px; font-weight: 600; }
                .opname-summary > div:nth-child(1), .opname-summary > div:nth-child(2) { font-size: .82rem; }
                .opname-lines-wrapper { overflow: visible; }
                .opname-lines, .opname-lines tbody { display: block; width: 100%; }
                .opname-lines { border: 0; }
                .opname-lines thead { display: none; }
                .opname-lines tfoot { display: none; }
                .opname-lines .opname-line-row {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                    column-gap: .75rem;
                    margin-bottom: .5rem;
                    padding: .625rem .75rem;
                    border: 1px solid #dce1e7;
                    border-radius: .6rem;
                    background: #fff;
                    box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .05);
                }
                .opname-lines.table-sm > tbody > tr.opname-line-row > td {
                    display: block;
                    min-width: 0;
                    padding: .15rem 0;
                    border: 0;
                    text-align: left !important;
                    white-space: normal;
                    overflow-wrap: anywhere;
                }
                .opname-lines .opname-line-row td::before {
                    display: block;
                    margin-bottom: .1rem;
                    color: #6c757d;
                    font-size: .72rem;
                    font-weight: 400;
                    content: attr(data-label);
                }
                .opname-lines.table-sm > tbody > tr.opname-line-row > td[data-label="Baris"] {
                    grid-column: 1;
                    grid-row: 1;
                    font-size: .8rem;
                    color: #6c757d;
                    white-space: nowrap;
                }
                .opname-lines .opname-line-row td[data-label="Baris"]::before {
                    display: inline;
                    margin-right: .25rem;
                    font-size: .8rem;
                    content: "Baris";
                }
                .opname-lines.table-sm > tbody > tr.opname-line-row > td[data-label="Box lama"] {
                    grid-column: 2;
                    grid-row: 1;
                    display: flex;
                    align-items: center;
                    min-width: 0;
                    font-size: 1rem;
                    font-weight: 700;
                    white-space: nowrap;
                }
                .opname-lines .opname-line-row td[data-label="Box lama"]::before {
                    display: inline;
                    flex: 0 0 auto;
                    margin-right: .3rem;
                    font-size: .72rem;
                    font-weight: 400;
                    color: #6c757d;
                    content: "BOX";
                }
                .opname-lines .opname-line-row .opname-box-no {
                    flex: 1 1 auto;
                    min-width: 0;
                    margin-right: .35rem;
                    overflow: hidden;
                    text-align: left;
                    text-overflow: ellipsis;
                }
                .opname-lines .opname-mobile-card-toggle {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    flex: 0 0 auto;
                    width: 40px;
                    height: 40px;
                    margin-left: 0;
                    padding: 0 0 .15rem;
                    border: 1px solid #dce1e7;
                    border-radius: .5rem;
                    background: #fff;
                    color: #495057;
                    font-size: 1.3rem;
                    line-height: 1;
                }
                .opname-lines .opname-mobile-card-toggle > span {
                    display: inline-block;
                    transition: transform .15s ease-in-out;
                }
                .opname-lines .opname-line-row.opname-mobile-expanded .opname-mobile-card-toggle > span { transform: rotate(90deg); }
                .opname-lines.table-sm > tbody > tr.opname-line-row > td.opname-desktop-primary { display: none; }
                .opname-lines.table-sm > tbody > tr.opname-line-row > td.opname-mobile-metrics {
                    display: grid;
                    grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, 1.15fr);
                    grid-column: 1 / -1;
                    grid-row: 2;
                    gap: .5rem;
                    margin-top: .4rem;
                    padding-top: .45rem;
                    border-top: 1px solid #eef0f3;
                }
                .opname-lines .opname-line-row td.opname-mobile-metrics::before {
                    display: none;
                    content: none;
                }
                .opname-mobile-metric { min-width: 0; }
                .opname-mobile-metric-label {
                    display: block;
                    margin-bottom: .1rem;
                    color: #6c757d;
                    font-size: 11px;
                    font-weight: 400;
                }
                .opname-mobile-metric-value {
                    display: block;
                    min-width: 0;
                    overflow: hidden;
                    font-size: .875rem;
                    line-height: 1.25;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                .opname-mobile-delta {
                    margin-left: .15rem;
                    font-size: .72rem;
                    white-space: nowrap;
                }
                .opname-lines.table-sm > tbody > tr.opname-line-row > td.opname-mobile-detail { display: none; }
                .opname-lines.table-sm > tbody > tr.opname-mobile-expanded > td.opname-mobile-detail { display: block; grid-column: 1 / -1; }
                .opname-lines .opname-line-row.opname-mobile-expanded td[data-label="Partai"] {
                    margin-top: .4rem;
                    padding-top: .45rem;
                    border-top: 1px dashed #dce1e7;
                }
                .opname-lines .opname-line-row.opname-mobile-expanded td[data-label="Partai"],
                .opname-lines .opname-line-actions { grid-column: 1 / -1; }
                .opname-lines .opname-line-actions::before { display: none; }
                .opname-lines .opname-line-actions .d-flex { flex-wrap: nowrap; }
                .opname-lines .opname-action-detail {
                    display: block;
                    flex: 1 1 0;
                    min-width: 0;
                }
                .opname-lines .opname-line-actions .d-flex > .btn { flex: 1 1 0; min-width: 0; }
                .opname-lines .opname-line-actions .d-flex > form { display: flex; flex: 1 1 0; min-width: 0; }
                .opname-lines .opname-line-actions .d-flex > form > .btn { flex: 1 1 0; min-width: 0; }
                .opname-lines .opname-line-actions .btn { min-height: 44px; }
                .opname-lines .opname-line-edit:not(.d-none) { display: block; margin: -.35rem 0 .75rem; }
                .opname-lines .opname-line-edit td {
                    display: block;
                    padding: .75rem;
                    border: 1px solid #b8c5d1;
                    border-radius: .6rem;
                    white-space: normal;
                    background: #f8f9fa;
                }
                .opname-lines .opname-line-edit .form-control,
                .opname-lines .opname-line-edit .btn { min-height: 44px; font-size: 16px; }
                .opname-action-bar {
                    margin-top: 2.5rem;
                    padding-top: 1.25rem;
                    border-top: 2px dashed #dee2e6;
                    background: transparent;
                    box-shadow: none;
                }
                .opname-action-bar .btn { min-height: 48px; width: 100%; font-weight: 600; }
            }
            @media print {
                .opname-no-print, .sidebar-wrapper, #sidebar, header, footer, .navbar { display: none !important; }
                #main { margin-left: 0 !important; padding: 0 !important; }
                .table-responsive { overflow: visible; }
                .opname-lines { font-size: 9px; }
                .card { border: 0 !important; box-shadow: none !important; }
                @page { size: landscape; margin: 10mm; }
            }
        </style>
    </x-slot>
</x-theme.app>
