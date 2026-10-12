<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <a href="{{ route('opname_menu.index') }}" class="btn btn-sm btn-outline-primary float-end">Kembali ke Opname</a>
    </x-slot>
    <x-slot name="cardBody">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <p class="mb-1">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form action="{{ route('opname_menu.destroy', $history->id) }}" method="POST" class="mb-3"
            onsubmit="return confirm('Batalkan import #{{ $history->id }} beserta seluruh alur transaksi {{ $history->jumlah_box }} box? Data import dan history akan dihapus.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete / Batalkan seluruh import</button>
        </form>
        <p class="text-muted">Pembatalan menghapus BK, tahapan Cabut/Cetak/Sortir yang dibuat import, formulir sarang, dan tb_hancuran. Jika barang sudah diproses lebih lanjut atau transaksinya berubah, pembatalan ditolak.</p>
        <h5>{{ $statuses[$history->tujuan] ?? $history->tujuan }}</h5>
        <p>Diimpor {{ $history->created_at->format('d-m-Y H:i:s') }} oleh <strong>{{ $history->user_name }}</strong>.
            File: {{ $history->file_name ?? '-' }}.</p>
        <p>{{ $history->jumlah_box }} box &middot; {{ number_format($history->total_pcs, 0) }} pcs &middot;
            {{ number_format($history->total_gr, 2) }} gram &middot; Rp {{ number_format($history->total_rp, 2) }}</p>
        @foreach ($history->invoices as $category => $numbers)
            @if ($numbers)
                <p>Formulir {{ ucfirst($category) }}: {{ implode(', ', $numbers) }}</p>
            @endif
        @endforeach
        <p class="text-muted">Data saat import disimpan. Pcs dan gram akhir Cabut mengikuti jumlah awal; biaya Cabut, Cetak, dan operasional 0. Untuk Sortir, pcs dan gram akhir Cetak juga sama dengan jumlah awal. Untuk sisa belum grading, Sortir juga selesai dengan jumlah awal/akhir sama dan biaya 0. Tanggal pada tiap barang berlaku untuk seluruh tahapannya.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead><tr><th>Baris Excel</th><th>No. Box</th><th>Partai</th><th>Tipe</th><th>Keterangan</th><th>Warna</th><th>Tanggal barang</th><th>Pengawas Cabut</th><th>Pcs</th><th>Gram</th><th>Harga/gr</th><th>Nilai barang</th>
                    @if (in_array($history->tujuan, ['cetak', 'sortir', 'grade']))
                        <th>Anak Cabut</th><th>Kelas Cabut</th><th>Pengawas Cetak</th>
                    @endif
                    @if (in_array($history->tujuan, ['sortir', 'grade']))
                        <th>Anak Cetak</th><th>Kelas Cetak</th><th>Pengawas Sortir</th>
                    @endif
                    @if ($history->tujuan === 'grade')
                        <th>Anak Sortir</th><th>Kelas Sortir</th><th>Pengawas Grading</th>
                    @endif
                </tr></thead>
                <tbody>
                    @foreach ($history->rows as $row)
                        <tr>
                            <td>{{ $row['_baris'] }}</td><td>{{ $row['no_box'] }}</td><td>{{ $row['nm_partai'] }}</td>
                            <td>{{ $row['tipe'] ?? '-' }}</td><td>{{ $row['ket'] ?? '-' }}</td><td>{{ $row['warna'] ?? '-' }}</td>
                            <td>{{ $row['tgl'] }}</td><td>{{ $history->tujuan === 'bk' ? 'Belum diserahkan' : ($row['pengawas_cabut_name'] ?? '-') }} ({{ $row['id_pengawas'] }})</td>
                            <td>{{ number_format($row['pcs_awal'], 0) }}</td><td>{{ number_format($row['gr_awal'], 2) }}</td>
                            <td>{{ number_format($row['hrga_satuan'], 2) }}</td><td>{{ number_format($row['gr_awal'] * $row['hrga_satuan'], 2) }}</td>
                            @if (in_array($history->tujuan, ['cetak', 'sortir', 'grade']))
                                <td>{{ $row['anak_cabut_name'] ?? '-' }} ({{ $row['id_anak_cabut'] }})</td>
                                <td>{{ $row['kelas_cabut_name'] ?? '-' }} ({{ $row['id_kelas_cabut'] }})</td>
                                <td>{{ $row['pengawas_cetak_name'] ?? '-' }} ({{ $row['id_pengawas_cetak'] }})</td>
                            @endif
                            @if (in_array($history->tujuan, ['sortir', 'grade']))
                                <td>{{ $row['anak_cetak_name'] ?? '-' }} ({{ $row['id_anak_cetak'] }})</td>
                                <td>{{ $row['kelas_cetak_name'] ?? '-' }} ({{ $row['id_kelas_cetak'] }})</td>
                                <td>{{ $row['pengawas_sortir_name'] ?? '-' }} ({{ $row['id_pengawas_sortir'] }})</td>
                            @endif
                            @if ($history->tujuan === 'grade')
                                <td>{{ $row['anak_sortir_name'] ?? '-' }} ({{ $row['id_anak_sortir'] }})</td>
                                <td>{{ $row['kelas_sortir_name'] ?? '-' }} ({{ $row['id_kelas_sortir'] }})</td>
                                <td>{{ $row['pengawas_grading_name'] ?? '-' }} ({{ $row['id_pengawas_grading'] }})</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-slot>
</x-theme.app>
