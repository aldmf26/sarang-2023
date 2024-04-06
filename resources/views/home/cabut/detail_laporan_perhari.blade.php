<div class="row"></div>
<div class="col-lg-3">
    <table class="table">
        <tr>
            <th width="1">Nama</th>
            <th width="1">:</th>
            <th>{{ $anak->nama }}</th>
        </tr>
        <tr>
            <th width="1">Kelas</th>
            <th width="1">:</th>
            <th>{{ $anak->id_kelas }}</th>
        </tr>
        <tr>
            <th width="100">Hari Masuk</th>
            <th width="1">:</th>
            <th>{{ $absen }}</th>
        </tr>
    </table>
</div>
<div class="col-lg-12">
    <table class="table table-bordered table-hover" id="tableHarian">
        <thead>
            <tr>
                <th class="dhead">Kerja</th>
                <th class="dhead">No Box</th>
                <th class="dhead text-end">Pcs Awal</th>
                <th class="dhead text-end">Gr Awal</th>
                <th class="dhead text-end">Pcs Akhir</th>
                <th class="dhead text-end">Gr Akhir</th>
                <th class="dhead text-end">Rp Selesai</th>
                <th class="dhead text-end">Rp Proses</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cabut as $i => $d)
                <tr>
                    <td class="text-start">Cabut</td>
                    <td class="text-start">{{ $d->no_box }}</td>
                    <td>{{ number_format($d->pcs_awal, 0) }}</td>
                    <td>{{ number_format($d->gr_awal, 0) }}</td>
                    <td>{{ number_format($d->pcs_akhir, 0) }}</td>
                    <td>{{ number_format($d->gr_akhir, 0) }}</td>
                    <td>{{ number_format($d->ttl_rp, 0) }}</td>
                    <td>{{ number_format($d->rp_target, 0) }}</td>
                </tr>
            @endforeach
            @foreach ($eo as $i => $d)
                <tr>
                    <td class="text-start">Eo</td>
                    <td class="text-start">{{ $d->no_box }}</td>
                    <td>{{ number_format(0, 0) }}</td>
                    <td>{{ number_format($d->gr_awal, 0) }}</td>
                    <td>{{ number_format(0, 0) }}</td>
                    <td>{{ number_format($d->gr_akhir, 0) }}</td>
                    <td>{{ number_format($d->ttl_rp, 0) }}</td>
                    <td>{{ number_format($d->rp_target, 0) }}</td>
                </tr>
            @endforeach
            @foreach ($sortir as $i => $d)
                <tr>
                    <td class="text-start">Sortir</td>
                    <td class="text-start">{{ $d->no_box }}</td>
                    <td>{{ number_format($d->pcs_awal, 0) }}</td>
                    <td>{{ number_format($d->gr_awal, 0) }}</td>
                    <td>{{ number_format($d->pcs_akhir, 0) }}</td>
                    <td>{{ number_format($d->gr_akhir, 0) }}</td>
                    <td>{{ number_format($d->ttl_rp, 0) }}</td>
                    <td>{{ number_format($d->rp_target, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
