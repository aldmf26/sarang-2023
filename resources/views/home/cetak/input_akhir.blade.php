<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">Nama Anak</th>
            <th class="dhead">No Box</th>
            {{-- <th class="bg-danger text-white text-end" width="80">Pcs <br> Tdk Ctk</th>
            <th class="bg-danger text-white text-end" width="80">Gr <br> Tdk Ctk</th> --}}
            <th class="dhead text-end" width="90">Pcs <br> awl Ctk</th>
            <th class="dhead text-end" width="80">Gr <br> awl Ctk</th>
            <th class="dhead text-end"width="80">Pcs akhir</th>
            <th class="dhead text-end"width="80">Gr akhir</th>
            <th class="dhead text-end"width="80">Pcs Cu</th>
            <th class="dhead text-end"width="80">Gr Cu</th>
            <th class="dhead text-end"width="80">Pcs hcr</th>
            <th class="dhead text-end"width="80">Susut</th>
            <th class="dhead text-end"width="100">Ttl Rp</th>
            <th class="dhead text-end"width="120">Dibayar</th>
            <th class="dhead text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $c)
            @php
                $susut = empty($c->gr_akhir) ? '0' : (1 - ($c->gr_akhir + $c->gr_cu) / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                $denda = round($susut, 0) >= $c->batas_susut ? round($susut) * $c->denda_susut : 0;
                $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
                $ttl_rp = $c->pcs_awal_ctk * $c->rp_pcs;
            @endphp
            <tr data-id="{{ $c->id_cetak }}">
                <td><span>{{ strtoupper($c->nama) }}
                        ({{ $c->id_kelas }})
                    </span>
                </td>
                <td>{{ $c->no_box }}</td>
                <td>
                    <input type="text" class="form-control text-end " name="pcs_awal_ctk"
                        value="{{ $c->pcs_awal_ctk == 0 ? $c->pcs_awal : $c->pcs_awal_ctk }}" readonly>
                    <input type="hidden" class="form-control text-end" name="id_cetak" value="{{ $c->id_cetak }}">
                    <input type="hidden" class="rp_pcs{{ $c->id_cetak }}" value="{{ $c->rp_pcs }}">
                </td>
                <td>
                    <input type="text" class="form-control text-end gr_awal gr_awal{{ $c->id_cetak }}"
                        name="gr_awal_ctk"value="{{ $c->gr_awal_ctk == 0 ? $c->gr_awal : $c->gr_awal_ctk }}"
                        count="{{ $c->id_cetak }}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control text-end pcs_awal pcs_awal{{ $c->id_cetak }}"
                        count="{{ $c->id_cetak }}" name="pcs_akhir"value="{{ $c->pcs_akhir }}">
                </td>
                <td>
                    <input type="text" class="form-control text-end gr_akhir gr_akhir{{ $c->id_cetak }}"
                        name="gr_akhir"value="{{ $c->gr_akhir }}" count="{{ $c->id_cetak }}">
                    <input type="hidden" class="denda_susut{{ $c->id_cetak }}" value="{{ $c->denda_susut }}">
                    <input type="hidden" class="batas_susut{{ $c->id_cetak }}" value="{{ $c->batas_susut }}">
                </td>
                <td>
                    <input type="text" class="form-control text-end " name="pcs_cu"value="{{ $c->pcs_cu }}">
                </td>
                <td>
                    <input type="text" class="form-control text-end gr_cu gr_cu{{ $c->id_cetak }}"
                        count="{{ $c->id_cetak }}" name="gr_cu"value="{{ $c->gr_cu }}">
                </td>
                <td>
                    <input type="text" class="form-control text-end pcs_hcr pcs_hcr{{ $c->id_cetak }}"
                        count="{{ $c->id_cetak }}" name="pcs_hcr"value="{{ $c->pcs_hcr }}">

                    <input type="hidden" class="form-control text-end denda_hcr{{ $c->id_cetak }}"
                        value="{{ $c->denda_hcr }}">
                </td>
                <td class="text-end"> <span class=" susut{{ $c->id_cetak }}">{{ round($susut) }}%</span></td>
                <td class="text-end">
                    <span
                        class=" ttl_rp{{ $c->id_cetak }}">{{ $c->pcs_awal_ctk == 0 ? $c->pcs_awal * $c->rp_pcs : $ttl_rp - $denda - $denda_hcr }}</span>
                </td>
                <td>
                    <select name="bulan_dibayar" id="" class="form-control">
                        <option value="">Pilih Bulan</option>
                        @foreach ($bulan as $b)
                            <option value="{{ $b->bulan }}"
                                {{ $b->bulan == $c->bulan_dibayar ? 'selected' : '' }}>
                                {{ $b->nm_bulan }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary btn_simpan">Simpan</button>
                    <button type="button" data-bs-target="#selesai" data-bs-toggle="modal"
                        id_cetak="{{ $c->id_cetak }}" class="btn btn-sm btn-success selesai"
                        {{ empty($c->pcs_akhir) || empty($c->gr_akhir) ? 'hidden' : '' }}>Selesai</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
