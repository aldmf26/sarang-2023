<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">Nama</th>
            <th class="dhead">paket</th>
            <th class="dhead text-end">pcs awal</th>
            <th class="dhead text-end">gr awal</th>
            <th width="130px" class="dhead text-end">pcs akhir</th>
            <th width="130px" class="dhead text-end">gr akhir</th>
            <th class="dhead text-end">sst%</th>
            <th class="dhead text-end">Total Rp</th>
            <th width="120px" class="dhead text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $no => $c)
            <tr data-id="{{ $c->id_cetak }}">
                <td>{{ $no + 1 }}</td>
                <td>{{ $c->no_box }}</td>
                <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                <td>{{ $c->nm_anak }}</td>
                <td>{{ $c->kelas }}</td>
                <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
                <td class="text-end">{{ $c->gr_awal_ctk }}</td>

                <td class="text-end">
                    <input type="text" class="form-control text-end pcs_akhir{{ $c->id_cetak }}" name="pcs_akhir[]"
                        value="{{ $c->pcs_akhir }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>

                    {{-- hidden --}}
                    <input type="hidden" class="form-control text-end pcs_awal{{ $c->id_cetak }}"
                        value="{{ $c->pcs_awal_ctk }}">
                    <input type="hidden" class="rp_satuan{{ $c->id_cetak }}" value="{{ $c->rp_satuan }}">
                    <input type="hidden" class="no{{ $c->id_cetak }}" value="{{ $no + 1 }}">
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end gr_akhir{{ $c->id_cetak }}" name="gr_akhir[]"
                        value="{{ $c->gr_akhir }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>
                <td class="text-end">
                    {{ empty($c->gr_akhir) ? 0 : number_format((1 - $c->gr_akhir / $c->gr_awal_ctk) * 100, 1) }}%</td>
                <td class="text-end">{{ number_format($c->pcs_akhir * $c->rp_satuan) }}</td>
                <td class="text-center">
                    <button type="button" {{ $c->selesai == 'Y' ? 'hidden' : '' }}
                        class="btn btn-sm btn-warning btn_save_akhir btn_save_akhir{{ $c->id_cetak }}"
                        id_cetak="{{ $c->id_cetak }}">Akhir
                    </button>

                    <button type="button" {{ $c->pcs_akhir == '0' || $c->selesai == 'Y' ? 'hidden' : '' }}
                        class="btn btn-sm btn-primary btn_selesai" id_cetak="{{ $c->id_cetak }}">Selesai
                    </button>

                    <button type="button" {{ $c->selesai == 'T' ? 'hidden' : '' }}
                        class="btn btn-sm btn-warning btn_cancel" id_cetak="{{ $c->id_cetak }}"><i
                            class="fas fa-redo"></i>
                    </button>

                </td>
            </tr>
        @endforeach

    </tbody>
</table>
