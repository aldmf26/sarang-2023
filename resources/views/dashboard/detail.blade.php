<x-theme.app title="{{ $title }}" table="T" sizeCard="12">
    <h6>{{ $title }}</h6>
    <table class="table table-hover table-bordered table-striped" id="table">
        <thead>
            @php
                $ttlPcsAwal = 0;
                $ttlGrAwal = 0;
                $ttlPcsAkhir = 0;
                $ttlGrAkhir = 0;
                $ttlEot = 0;
                $ttlFlx = 0;
                $ttlTtlRp = 0;
                foreach ($query as $d) {
                    $ttlPcsAwal += $d->pcs_awal;
                    $ttlGrAwal += $d->gr_awal;
                    $ttlPcsAkhir += $d->pcs_akhir;
                    $ttlGrAkhir += $d->gr_akhir;
                    $ttlEot += $d->eot;
                    $ttlFlx += $d->gr_flx;
                    $ttlTtlRp += $d->ttl_rp;
                }
            @endphp
            <tr>
                <th class="dhead">No Box</th>
                <th class="dhead">Pgws</th>
                <th class="dhead">Nama Anak</th>
                <th class="dhead">Dibayar</th>
                <th class="dhead text-end">Pcs Awal ({{ number_format($ttlPcsAwal,0) }})</th>
                <th class="dhead text-end">Gr Awal ({{ number_format($ttlGrAwal,0) }})</th>
                <th class="dhead text-end">Pcs Akhir ({{ number_format($ttlPcsAkhir,0) }})</th>
                <th class="dhead text-end">Gr Akhir ({{ number_format($ttlGrAkhir,0) }})</th>
                <th class="dhead text-end">Sst</th>
                <th class="dhead text-end">Eot ({{ number_format($ttlEot,0) }})</th>
                <th class="dhead text-end">Flx ({{ number_format($ttlFlx,0) }})</th>
                <th class="dhead text-end">Cost Cbt ({{ number_format($ttlTtlRp,0) }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($query as $i => $d)
                @php
                    $bulanDibayar = date('M Y', strtotime('01-' . $d->bulan . '-' . date('Y', strtotime($d->tahun))));
                    $pcs_awal = $d->pcs_awal;
                    $gr_awal = $d->gr_awal;
                    $pcs_akhir = $d->pcs_akhir;
                    $gr_akhir = $d->gr_akhir;
                    $gr_flx = $d->gr_flx;
                    $eot = $d->eot;
                    $ttl_rp = $d->ttl_rp;
                    $sst = empty($gr_awal) ? 0 : (1 - ($gr_flx + $gr_akhir) / $gr_awal) * 100;

                @endphp

                <tr>
                    <td>{{ $d->no_box }}</td>
                    <td>{{ $d->pgws }}</td>
                    <td>{{ $d->nm_anak }}</td>
                    <td>{{ $bulanDibayar }}</td>
                    <td class="text-end">{{ number_format($pcs_awal, 0) }}</td>
                    <td class="text-end">{{ number_format($gr_awal, 0) }}</td>
                    <td class="text-end">{{ number_format($pcs_akhir, 0) }}</td>
                    <td class="text-end">{{ number_format($gr_akhir, 0) }}</td>
                    <td class="text-end">{{ number_format($sst, 0) }}%</td>
                    <td class="text-end">{{ number_format($eot, 0) }}</td>
                    <td class="text-end">{{ number_format($gr_flx, 0) }}</td>
                    <td class="text-end">{{ number_format($ttl_rp, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-theme.app>
