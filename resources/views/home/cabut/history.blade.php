<div class="row">
    <div class="col-lg-12">
        <table class="table table-hover table-bordered" style="border:1px solid #97a1c3" id="tableHistory">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead" width="50">Bulan</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Anak</th>
                    <th class="dhead">Tgl Terima Brg</th>
                    <th class="text-end dhead">Pcs Awal</th>
                    <th class="text-end dhead">Gr Awal</th>
                    <th class="text-end dhead">Gr Flx</th>
                    <th class="text-end dhead">Pcs Akhir</th>
                    <th class="text-end dhead">Gr Akhir</th>
                    <th class="text-end dhead">EOT</th>
                    <th class="text-end dhead">Pcs Hcr</th>
                    <th class="text-end dhead">Susut</th>
                    <th class="text-end dhead">Rp Trgt</th>
                    <th class="text-end dhead">Total Gaji</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($cabut as $no => $d)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ !empty($d->bulan_dibayar) ? date('M y', strtotime('01-' . $d->bulan_dibayar . '-' . date('Y'))) : '' }}
                        </td>
                        <td>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
                                id_cabut="{{ $d->id_cabut }}">{{ $d->no_box }}</a>
                        </td>
                        <td>{{ $d->nama }}</td>
                        <td>{{ date('d M y', strtotime($d->tgl_terima)) }}</td>
                        <td align="right">{{ $d->pcs_awal }}</td>
                        <td align="right">{{ $d->gr_awal }}</td>
                        <td align="right">{{ $d->gr_flx }}</td>
                        <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                        <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                        @php
                            if (!empty($d->eot)) {
                                $warna = $d->eot < rumusTotalRp($d)->batas_eot ? 'text-danger' : '';
                            }
                        @endphp
                        <td align="right" class="{{ $warna ?? '' }}">{{ $d->eot ?? 0 }}</td>
                        <td align="right">{{ $d->pcs_hcr ?? 0 }}</td>
                        @php
                            $hasil = rumusTotalRp($d);
                        @endphp
                        <td align="right">{{ number_format($hasil->susut, 0) }}%</td>
                        <td align="right">{{ number_format($d->rupiah, 0) }}
                        <td align="right">{{ number_format($hasil->ttl_rp, 0) }}
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
