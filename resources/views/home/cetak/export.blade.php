<table class="table table-bordered" id="table1">
    <thead>
        <tr>
            <th class="">#</th>
            <th class="">Bulan</th>
            <th class="">No Box</th>
            <th class="">Grade</th>
            <th class="">ID anak</th>
            <th class="">Nama</th>
            <th class="">Kelas</th>
            <th class=" text-center">Pcs Tdk Ctk</th>
            <th class=" text-center">Gr Tdk Ctk</th>
            <th class="">Tgl Terima Brg</th>
            <th class=" text-center">Pcs Awal </th>
            <th class=" text-center">Gr Awal </th>
            <th class=" text-end">Pcs Cu</th>
            <th class=" text-end">Gr Cu</th>
            <th class=" text-center">Pcs Akhir</th>
            <th class=" text-center">Gr Akhir</th>
            <th class=" text-center">harga/pcs</th>
            <th class=" text-end">Pcs Hcr</th>
            <th class=" text-end">Susut</th>
            <th class=" text-end">Ttl Gaji</th>
            <th class=" text-end">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $c)
            @php
                $susut = empty($c->gr_akhir) ? '0' : (1 - ($c->gr_akhir + $c->gr_cu) / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                $denda = round($susut, 0) >= $c->batas_susut ? round($susut) * $c->denda_susut : 0;
                $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
                $ttl_rp = $c->pcs_akhir == '0' ? $c->pcs_awal_ctk * $c->rp_pcs : $c->pcs_akhir * $c->rp_pcs;
            @endphp
            <tr>
                <td>{{ $c->id_cetak }}</td>
                <td>{{ !empty($c->bulan_dibayar) ? date('M y', strtotime('01-' . $c->bulan_dibayar . '-' . date('Y'))) : '' }}
                </td>
                <td>{{ $c->no_box }}</td>
                <td>{{ $c->grade }}</td>
                <td>{{ $c->id_anak }} </td>
                <td>{{ $c->nama }} </td>
                <td>{{ $c->id_kelas }}</td>
                {{-- <td class="text-end">{{ $c->pcs_awal }}</td>
                <td class="text-end">{{ $c->gr_awal }}</td> --}}
                <td class="text-end">{{ $c->pcs_tidak_ctk }}</td>
                <td class="text-end">{{ $c->gr_tidak_ctk }}</td>
                <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
                <td class="text-end">{{ $c->gr_awal_ctk }}</td>
                <td class="text-end">{{ $c->pcs_cu }}</td>
                <td class="text-end">{{ $c->gr_cu }}</td>
                <td class="text-end">{{ $c->pcs_akhir }}</td>
                <td class="text-end">{{ $c->gr_akhir }}</td>
                <td class="text-end">{{ $c->rp_pcs }}</td>

                <td class="text-end">{{ $c->pcs_hcr }}</td>
                <td class="text-end">{{ round($susut) }}%</td>
                <td class="text-end">{{ $ttl_rp - $denda - $denda_hcr }}</td>
                <td class="text-end">
                    @if ($c->selesai == 'Y')
                        SELESAI
                    @else
                        AKHIR
                    @endif
                </td>
            </tr>
        @endforeach


    </tbody>

</table>
