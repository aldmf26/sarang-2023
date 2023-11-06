<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">Bulan</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Nama</th>
            <th class="dhead">Tgl Terima Brg</th>
            <th class="dhead text-center">Pcs <br> Tdk Ctk</th>
            <th class="dhead text-center">Gr <br> Tdk Ctk</th>
            <th class="dhead text-center">Pcs <br> Awal </th>
            <th class="dhead text-center">Gr <br> Awal </th>
            <th class="dhead text-center">Pcs <br> Akhir</th>
            <th class="dhead text-center">Gr <br> Akhir</th>
            <th class="dhead text-end">Pcs Cu</th>
            <th class="dhead text-end">Gr Cu</th>
            <th class="dhead text-end">Pcs Hcr</th>
            <th class="dhead text-end">Susut</th>
            <th class="dhead text-end">Ttl Gaji</th>
            <th class="dhead">Selesai</th>
            <th class="dhead text-center"><input style="text-align: center" type="checkbox" class="form-check"
                    id="cekSemuaTutup">
                <span class="badge bg-danger btn_tutup d-none" tipe="tutup" style="cursor: pointer"><i
                        class="fas fa-check"></i> Tutup </span>
                <span class="badge bg-danger btn_tutup d-none mt-3" tipe="cancel_data"
                    style="cursor: pointer">Cancel</span>
            </th>

        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $no => $c)
            @php
                $susut = empty($c->gr_akhir) ? '0' : (1 - ($c->gr_akhir + $c->gr_cu) / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                $denda = round($susut, 0) >= $c->batas_susut ? round($susut) * $c->denda_susut : 0;
                $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
                $ttl_rp = $c->pcs_akhir == '0' ? $c->pcs_awal_ctk * $c->rp_pcs : $c->pcs_akhir * $c->rp_pcs;
            @endphp
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ !empty($c->bulan_dibayar) ? date('M y', strtotime('01-' . $c->bulan_dibayar . '-' . date('Y'))) : '' }}
                </td>
                <td>{{ $c->no_box }}</td>
                <td>{{ $c->nama }} ({{ $c->kelas }})</td>
                <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                {{-- <td class="text-end">{{ $c->pcs_awal }}</td>
                <td class="text-end">{{ $c->gr_awal }}</td> --}}
                <td class="text-end">{{ $c->pcs_tidak_ctk }}</td>
                <td class="text-end">{{ $c->gr_tidak_ctk }}</td>
                <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
                <td class="text-end">{{ $c->gr_awal_ctk }}</td>
                <td class="text-end">{{ $c->pcs_akhir }}</td>
                <td class="text-end">{{ $c->gr_akhir }}</td>
                <td class="text-end">{{ $c->pcs_cu }}</td>
                <td class="text-end">{{ $c->gr_cu }}</td>
                <td class="text-end">{{ $c->pcs_hcr }}</td>
                <td class="text-end">{{ round($susut) }}%</td>
                <td class="text-end">{{ number_format($ttl_rp - $denda - $denda_hcr, 0) }}</td>
                <td class="text-end">
                    @if ($c->selesai == 'Y')
                        <span class="badge bg-primary">SELESAI</span>
                    @else
                        <a class="btn btn-warning btn-sm inputAkhir" href="#" id_cetak="{{ $c->id_cetak }}"
                            href="#" data-bs-toggle="modal" data-bs-target="#inputAkhir"></i>Akhir</a>
                    @endif
                </td>
                <td>
                    @if ($c->selesai == 'T')
                    @else
                        <input type="checkbox" class="form-check cekTutup" name="cekTutup[]"
                            id_cetak="{{ $c->id_cetak }}">
                    @endif


                </td>
            </tr>
        @endforeach


    </tbody>

</table>
