<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        @if (session('sukses'))
            <div class="alert alert-success">{{ session('sukses') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Proses belum berhasil.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('opname_cabut_invoices'))
            <p>No. formulir Cabut: {{ implode(', ', session('opname_cabut_invoices')) }}</p>
        @endif
        @if (session('opname_cetak_invoices'))
            <p>No. formulir Cetak: {{ implode(', ', session('opname_cetak_invoices')) }}</p>
        @endif
        @if (session('opname_sortir_invoices'))
            <p>No. formulir Sortir: {{ implode(', ', session('opname_sortir_invoices')) }}</p>
        @endif
        @if (session('opname_grade_invoices'))
            <p>No. formulir Grade: {{ implode(', ', session('opname_grade_invoices')) }}</p>
        @endif
        @if (session('opname_history_id'))
            <p><a href="{{ route('opname_menu.history', session('opname_history_id')) }}">Lihat detail import yang baru disimpan</a></p>
        @endif
        <div class="row g-4">
            <div class="col-lg-5">
                <h5>Import barang ke gudang</h5>
                <p class="text-muted">Pilih gudang tujuan dan gunakan template yang sesuai. Untuk Cabut dan tahapan berikutnya, seluruh tahapan sebelumnya dicatat melalui formulir sarang. Nilai barang hanya memakai gram awal BK &times; hrga_satuan; biaya Cabut, Cetak, dan operasional otomatis 0.</p>
                <form action="{{ route('opname_menu.preview_cabut') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="tujuan" class="form-label">Gudang tujuan</label>
                        <select id="tujuan" name="status" class="form-select" aria-label="Gudang tujuan" required>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $preview['status'] ?? 'stok') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel</label>
                        <input type="file" id="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                        <small class="text-muted">Maksimal 5 MB dan 500 baris. Satu baris untuk satu box.</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('opname_menu.template_bk') }}" class="btn btn-outline-primary">Download template BK sisa</a>
                        <a href="{{ route('opname_menu.template_cabut') }}" class="btn btn-outline-primary">Download template Cabut</a>
                        <a href="{{ route('opname_menu.template_cetak') }}" class="btn btn-outline-primary">Download template Cetak</a>
                        <a href="{{ route('opname_menu.template_sortir') }}" class="btn btn-outline-primary">Download template Sortir</a>
<a href="{{ route('opname_menu.template_grading') }}" class="btn btn-outline-primary">Download template Belum Grading</a>
                        <button type="submit" class="btn btn-primary">Periksa &amp; pratinjau</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-7">
                <div class="bg-light rounded p-3 mb-3">
                    <h6>Import BK sisa</h6>
                    <p class="mb-2">Stok BK yang belum diserahkan ke pengawas. Penerima 0 dan formulir T; belum ada formulir sarang atau hancuran. Nilai stok = gram &times; hrga_satuan.</p>
                    <hr>
                    <h6>Alur import Cabut</h6>
                    <p class="mb-2">BK &rarr; Formulir Sarang (Cabut) &rarr; Cabut sisa pengawas</p>
                    <p class="mb-0">Barang sudah diterima pengawas dan belum dibagikan ke anak. Semua baris harus valid sebelum dapat disimpan.</p>
                    <hr>
                    <h6>Alur import Cetak</h6>
                    <p class="mb-2">BK &rarr; Formulir Cabut &rarr; Cabut selesai &rarr; Formulir Cetak &rarr; Cetak sisa pengawas</p>
                    <p class="mb-0">Pengawas Cabut otomatis Arbayah (ID 285). Anak dipilih acak dari anak aktif Arbayah, dan kelas Cabut selalu ID 126. Pcs awal dan akhir Cabut otomatis sama. Gram awal dan akhir Cabut otomatis sama, dan penerimaan Cetak memakai gram yang sama. Barang belum dibagikan ke anak Cetak.</p>
                    <hr>
                    <h6>Alur import Sortir</h6>
                    <p class="mb-2">BK &rarr; Formulir Cabut &rarr; Cabut selesai &rarr; Formulir Cetak &rarr; Cetak selesai &rarr; Formulir Sortir &rarr; Sortir sisa pengawas</p>
                    <p class="mb-0">Cabut memakai Arbayah dan kelas 126. Pilih pengawas Cetak, kelas Cetak kategori CTK, dan pengawas penerima Sortir di template. Semua pengawas boleh menerima Sortir, tanpa batasan bagian. Anak Cetak dipilih acak dari anak aktif pengawas Cetak. Pcs, gram, dan tanggal sama untuk semua tahapan; biaya 0. Barang belum dibagikan ke anak Sortir.</p>
                    <hr>
                    <h6>Alur import sisa belum grading</h6>
                    <p class="mb-2">BK &rarr; Cabut selesai &rarr; Cetak selesai &rarr; Sortir selesai &rarr; Formulir Grade &rarr; Sisa belum grading</p>
                    <p class="mb-0">Semua tahapan memiliki formulir sarang dan tb_hancuran dengan pcs 0. Sortir lewat memakai anak Emo (ID 2), kelas lewat (ID 11). Pilih pengawas penerima grading di template. Pcs dan gram awal/akhir sama, tanggal sama, dan seluruh biaya 0. Hasil grading belum dibuat.</p>
                </div>
                <details>
                    <summary>Petunjuk pengisian dan ID pengawas</summary>
                    <p class="mt-2">Isi satu kolom tgl dengan format YYYY-MM-DD untuk seluruh tahapan, angka tanpa pemisah ribuan, dan harga BK per gram. Pcs boleh 0; gram wajib lebih dari 0. Untuk import Cetak, pengawas Cabut, anak, dan kelas diisi otomatis. Pilihan anak terlihat di pratinjau sebelum disimpan.</p>
                    <p>Pemberi formulir: <strong>{{ auth()->user()->name }}</strong>. Penerima: pengawas sesuai kolom <code>id_pengawas</code>.</p>
                    <table class="table table-sm">
                        <thead><tr><th>ID pengawas</th><th>Nama</th></tr></thead>
                        <tbody>
                            @foreach ($pengawas as $user)
                                <tr><td>{{ $user->id }}</td><td>{{ $user->name }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table table-sm">
                        <thead><tr><th>ID pengawas Cetak</th><th>Nama</th></tr></thead>
                        <tbody>
                            @foreach ($pengawasCetak as $user)
                                <tr><td>{{ $user->id }}</td><td>{{ $user->name }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </details>
            </div>
        </div>
        @if ($preview)
            <hr class="my-4">
            <h5>Pratinjau import {{ $statuses[$preview['status']] }}</h5>
            <p>{{ count($preview['rows']) }} box &middot; {{ $statuses[$preview['status']] }} &middot;
                {{ number_format(array_sum(array_column($preview['rows'], 'pcs_awal')), 0) }} pcs &middot;
                {{ number_format(array_sum(array_column($preview['rows'], 'gr_awal')), 2) }} gram awal</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Baris</th><th>No. Box</th><th>Partai</th><th>Pengawas Cabut</th><th>Tgl seluruh tahapan</th><th>Pcs awal</th><th>Gr awal</th><th>Rp/gr BK</th>
                        @if (in_array($preview['status'], ['cetak', 'sortir', 'grade']))
                            <th>Anak / Kelas Cabut</th><th>Pcs akhir Cabut</th><th>Gr akhir Cabut / Terima Cetak</th><th>Pengawas Cetak</th>
                        @endif
                        @if (in_array($preview['status'], ['sortir', 'grade']))
                            <th>Anak / Kelas Cetak</th><th>Pengawas Sortir</th>
                        @endif
                        @if ($preview['status'] === 'grade')
                            <th>Anak / Kelas Sortir</th><th>Pengawas Grading</th>
                        @endif
                    </tr></thead>
                    <tbody>
                        @foreach ($preview['rows'] as $row)
                            <tr>
                                <td>{{ $row['_baris'] }}</td><td>{{ $row['no_box'] }}</td><td>{{ $row['nm_partai'] }}</td>
                                <td>{{ $preview['status'] === 'bk' ? 'Belum diserahkan' : ($pengawas->firstWhere('id', $row['id_pengawas'])->name ?? $row['id_pengawas']) }}</td>
                                <td>{{ $row['tgl'] }}</td>
                                <td>{{ $row['pcs_awal'] }}</td><td>{{ $row['gr_awal'] }}</td><td>{{ $row['hrga_satuan'] }}</td>
                                @if (in_array($preview['status'], ['cetak', 'sortir', 'grade']))
                                    <td>{{ $anakCabut->firstWhere('id_anak', $row['id_anak_cabut'])->nama ?? $row['id_anak_cabut'] }} ({{ $row['id_anak_cabut'] }}) / {{ $row['id_kelas_cabut'] }}</td>
                                    <td>{{ $row['pcs_awal'] }}</td>
                                    <td>{{ $row['gr_awal'] }}</td>
                                    <td>{{ $pengawasCetak->firstWhere('id', $row['id_pengawas_cetak'])->name ?? $row['id_pengawas_cetak'] }}</td>
                                @endif
                                @if (in_array($preview['status'], ['sortir', 'grade']))
                                    <td>{{ $anakCetak->firstWhere('id_anak', $row['id_anak_cetak'])->nama ?? $row['id_anak_cetak'] }} ({{ $row['id_anak_cetak'] }}) / {{ $kelasCetak->firstWhere('id_kelas_cetak', $row['id_kelas_cetak'])->kelas ?? $row['id_kelas_cetak'] }} ({{ $row['id_kelas_cetak'] }})</td>
                                    <td>{{ $pengawasSortir->firstWhere('id', $row['id_pengawas_sortir'])->name ?? $row['id_pengawas_sortir'] }}</td>
                                @endif
                                @if ($preview['status'] === 'grade')
                                    <td>Emo ({{ $row['id_anak_sortir'] }}) / lewat ({{ $row['id_kelas_sortir'] }})</td>
                                    <td>{{ $pengawasSortir->firstWhere('id', $row['id_pengawas_grading'])->name ?? $row['id_pengawas_grading'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted">Data baru disimpan setelah tombol di bawah ditekan. Pratinjau berlaku 30 menit.</p>
            <form action="{{ route('opname_menu.import_cabut') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $preview['token'] }}">
                <button type="submit" class="btn btn-success">Simpan {{ count($preview['rows']) }} box ke {{ $statuses[$preview['status']] }}</button>
            </form>
        @endif
        <hr class="my-4">
        <h5>Riwayat import</h5>
        <p class="text-muted">Riwayat import yang berhasil disimpan melalui menu ini. Detail menyimpan data saat import, termasuk anak dan kelas yang digunakan.</p>
        <form method="GET" action="{{ route('opname_menu.index') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <label for="history_tujuan" class="form-label">Gudang tujuan</label>
                <select name="history_tujuan" id="history_tujuan" class="form-select">
                    <option value="">Semua tujuan</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('history_tujuan') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="history_dari" class="form-label">Dari tanggal import</label>
                <input type="date" id="history_dari" name="history_dari" value="{{ request('history_dari') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="history_sampai" class="form-label">Sampai tanggal import</label>
                <input type="date" id="history_sampai" name="history_sampai" value="{{ request('history_sampai') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary" type="submit">Filter</button>
                <a href="{{ route('opname_menu.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead><tr><th>ID</th><th>Waktu import</th><th>Tujuan</th><th>Pengguna</th><th>File</th><th>Box</th><th>Pcs</th><th>Gram</th><th>Nilai barang</th><th>Detail</th></tr></thead>
                <tbody>
                    @forelse ($history as $batch)
                        <tr>
                            <td>{{ $batch->id }}</td><td>{{ $batch->created_at->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $statuses[$batch->tujuan] ?? $batch->tujuan }}</td><td>{{ $batch->user_name }}</td><td>{{ $batch->file_name ?? '-' }}</td>
                            <td>{{ $batch->jumlah_box }}</td><td>{{ number_format($batch->total_pcs, 0) }}</td>
                            <td>{{ number_format($batch->total_gr, 2) }}</td><td>Rp {{ number_format($batch->total_rp, 2) }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('opname_menu.history', $batch->id) }}">Lihat</a>
                                <form action="{{ route('opname_menu.destroy', $batch->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Batalkan import #{{ $batch->id }} beserta seluruh alur transaksi {{ $batch->jumlah_box }} box? Data import dan history akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted">Belum ada riwayat import untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $history->links() }}
    </x-slot>
</x-theme.app>
