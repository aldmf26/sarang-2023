<table class="table" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">Bulan</th>
            <th class="dhead">No Box</th>
            {{-- <th>Pengawas</th> --}}
            <th class="dhead">Anak</th>
            <th class="dhead">Tgl Terima</th>
            <th class="text-end dhead">Pcs Awal</th>
            <th class="text-end dhead">Gr Awal</th>
            <th class="text-end dhead">Pcs Akhir</th>
            <th class="text-end dhead">Gr Akhir</th>
            <th class="text-end dhead">EOT</th>
            <th class="text-end dhead">Susut</th>
            {{-- <th class="text-end">Denda</th> --}}
            <th class="text-end dhead">Ttl Gaji</th>
            <th class="text-center dhead">Selesai</th>
            <th class="text-center dhead">
                <center>
                    <input style="text-align: center" type="checkbox" class="form-check" id="cekSemuaTutup2">
                </center>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ !empty($d->bulan_dibayar) ? date('M y', strtotime('01-' . $d->bulan_dibayar . '-' . date('Y'))) : '' }}
                </td>
                <td>
                    <a href="#" class="detail" data-bs-toggle="modal" data-bs-target="#detail"
                        id_cabut="{{ $d->id_cabut_spesial }}">{{ $d->no_box }}</a>
                </td>
                {{-- <td>{{ ucwords(auth()->user()->name) }}</td> --}}
                <td>{{ $d->nama }}</td>
                <td>{{ date('d M y', strtotime($d->tgl)) }}</td>
                <td align="right">{{ $d->pcs_awal }}</td>
                <td align="right">{{ $d->gr_awal }}</td>
                <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                <td align="right">{{ $d->eot ?? 0 }}</td>
                @php
                    $susut = empty($d->gr_akhir) ? 0 : (1 - ($d->gr_flex + $d->gr_akhir) / $d->gr_awal) * 100;
                @endphp
                <td align="right">{{ number_format($susut, 0) }}%</td>
                {{-- <td align="right">{{ number_format($denda,0)}}</td> --}}
                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                <td align="center">
                    <span
                        class="badge bg-{{ $d->selesai == 'Y' ? 'primary' : 'warning' }}">{{ $d->selesai == 'Y' ? 'SELESAI' : 'BELUM' }}</span>
                </td>
                <td align="center">
                    @if ($d->selesai == 'Y')
                        <input type="checkbox" class="form-check cekTutup2" name="cekTutup[]"
                            id_cabut="{{ $d->id_cabut_spesial }}">
                    @else
                        <a class="btn btn-warning btn-sm inputAkhir" href="#"
                            id_cabut="{{ $d->id_cabut_spesial }}" href="#" data-bs-toggle="modal"
                            data-bs-target="#inputAkhir"></i>Akhir</a>
                    @endif


                </td>

            </tr>
        @endforeach
    </tbody>

</table>
