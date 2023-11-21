<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Tipe</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead">Anak</th>
            <th class="dhead">Tgl Terima</th>
            <th class="text-end dhead">Pcs Awal</th>
            <th class="text-end dhead">Gr Awal</th>
            <th class="text-end dhead">Pcs Akhir</th>
            <th class="text-end dhead">Gr Akhir</th>
            <th class="text-end dhead">Susut</th>
            <th class="text-end dhead">Denda</th>
            <th class="text-end dhead">Ttl Gaji</th>
            <th class="dhead">Selesai</th>
            <th class="dhead" width="70">
                <center>
                    @php
                        $adaDitutup = DB::table('sortir')
                            ->where([['selesai', 'Y'], ['penutup', 'T']])
                            ->first();
                    @endphp
                    @if (!empty($adaDitutup))
                        <input style="text-align: center" type="checkbox" class="form-check" id="cekSemuaTutup">
                    @endif
                    <br>
                    <span class="badge bg-danger btn_tutup d-none" tipe="tutup" style="cursor: pointer"><i
                            class="fas fa-check"></i> Tutup </span>
                    <span class="badge bg-danger btn_tutup d-none mt-3" tipe="cancel_data"
                        style="cursor: pointer">Cancel</span>

                </center>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
                        id_sortir="{{ $d->id_sortir }}">{{ $d->no_box }}</a>
                </td>
                <td>{{ $d->kelas }}</td>
                <td>{{ ucwords(auth()->user()->name) }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->tgl }}</td>
                <td align="right">{{ $d->pcs_awal ?? 0 }}</td>
                <td align="right">{{ $d->gr_awal ?? 0 }}</td>
                <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                @php
                    $susut = empty($d->gr_akhir) ? 0 : (1 - $d->gr_akhir / $d->gr_awal) * 100;
                @endphp
                <td align="right">{{ number_format($susut, 0) }}%</td>
                <td align="right">{{ number_format($d->denda_sp ?? 0, 0) }}</td>
                <td align="right">{{ number_format($d->ttl_rp ?? 0, 0) }}</td>
                <td align="center">
                    @if ($d->selesai == 'T')
                        <a class="btn btn-warning btn-sm inputAkhir" href="#" gr_awal="{{ $d->gr_awal }}"
                            gr_akhir="{{ $d->gr_akhir ?? 0 }}" no_box="{{ $d->no_box }}"
                            id_anak="{{ $d->id_anak }}" href="#" data-bs-toggle="modal"
                            data-bs-target="#inputAkhir"></i>Akhir</a>
                    @else
                        <span class="badge bg-primary">SELESAI</span>
                    @endif
                </td>
                <td align="center">
                    @if ($d->selesai != 'T')
                        <input type="checkbox" class="form-check cekTutup" name="cekTutup[]"
                            id_sortir="{{ $d->id_sortir }}">
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
