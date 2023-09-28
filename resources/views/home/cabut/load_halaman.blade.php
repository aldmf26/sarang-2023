<table class="table" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>No Box</th>
            <th>Anak</th>
            <th>Tgl Terima Brg</th>
            <th class="text-end">Pcs Awal</th>
            <th class="text-end">Gr Awal</th>
            <th class="text-end">Gr Flx</th>
            <th class="text-end">Pcs Akhir</th>
            <th class="text-end">Gr Akhir</th>
            <th class="text-end">EOT</th>
            <th class="text-end">Pcs Hcr</th>
            <th class="text-end">Susut</th>
            <th class="text-end">Rp Trgt</th>
            <th class="text-end">Ttl Gaji</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ date('d M y', strtotime($d->tgl_terima)) }}</td>
                <td align="right">{{ $d->pcs_awal }}</td>
                <td align="right">{{ $d->gr_awal }}</td>
                <td align="right">{{ $d->gr_flx }}</td>
                <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                <td align="right">{{ $d->eot ?? 0 }}</td>
                <td align="right">{{ $d->pcs_hcr ?? 0 }}</td>
                @php
                    $hasil = rumusTotalRp($d);
                @endphp
                <td align="right">{{ number_format($hasil->susut, 0) }}%</td>
                <td align="right">{{ number_format($d->rupiah, 0) }}
                <td align="right">{{ number_format($hasil->ttl_rp, 0) }}
                </td>
                <td align="center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                        class="btn btn-sm btn-primary detail" id_cabut="{{ $d->id_cabut }}"><i
                            class="fas fa-eye"></i></a>
                    @if ($d->selesai == 'T')
                        <a class="btn btn-warning btn-sm inputAkhir" href="#" no_box="{{ $d->no_box }}"
                            id_anak="{{ $d->id_anak }}" href="#" data-bs-toggle="modal"
                            data-bs-target="#inputAkhir"></i>Akhir</a>

                        @if (!empty($d->eot))
                            <a class="btn btn-primary btn-sm selesai" href="#" id_cabut="{{ $d->id_cabut }}"
                                href="#" data-bs-toggle="modal" data-bs-target="#selesai"></i>Selesai</a>
                        @endif
                    @endif



                </td>
            </tr>
        @endforeach
    </tbody>

</table>
