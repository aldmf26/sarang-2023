<tr data-id="{{ $d->id_sortir }}">
    <td>{{ $no }}</td>
    <td>
        <input type="date" value="{{ $d->tgl }}" class="form-control tgl{{ $d->id_sortir }}" name="tgl_cabut[]">
        {{-- {{ date('d M Y', strtotime($d->tgl)) }} --}}
    </td>
    <td>
        <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
            id_sortir="{{ $d->id_sortir }}">{{ $d->no_box }}</a>
    </td>
    <td>
        @if ($d->selesai == 'Y')
                        {{$d->nama}}
                    @else
                    <select name="id_anak[]" id="" class="select2_add id_anak{{ $d->id_sortir }}"
                        {{ $d->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih Anak</option>
                        @foreach ($anak as $a)
                            <option value="{{ $a->id_anak }}" {{ $a->id_anak == $d->id_anak ? 'selected' : '' }}>
                                {{ ucwords($a->nama) }}</option>
                        @endforeach
                    </select>
                    @endif
    </td>
    <td>
        <select name="" id="" class="form-control id_kelas{{ $d->id_sortir }}"
            {{ $d->selesai == 'Y' ? 'disabled' : '' }}>
            <option value="">pilih</option>
            @foreach ($kelas as $v)
                <option value="{{ $v->id_kelas }}" {{ $d->id_kelas == $v->id_kelas ? 'selected' : '' }}>
                    {{ strtoupper($v->kelas) }}</option>
            @endforeach
        </select>
        {{-- {{ $d->kelas }} --}}
    </td>
    {{-- <td>{{ ucwords(auth()->user()->name) }}</td> --}}


    <td align="right">{{ $d->pcs_awal ?? 0 }}</td>
    <td align="right">{{ $d->gr_awal ?? 0 }}</td>
    <td align="right">

        <input type="hidden" class="form-control text-end pcs_awal{{ $d->id_sortir }}"
            value="{{ $d->pcs_awal ?? 0 }}">
        <input type="text" class="form-control text-end pcs_akhir{{ $d->id_sortir }}"
            value="{{ empty($d->pcs_akhir) ? $d->pcs_awal : $d->pcs_akhir }}"
            {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
    </td>
    <td align="right">

        <input type="text" class="form-control text-end gr_akhir{{ $d->id_sortir }}"
            value="{{ $d->gr_akhir ?? 0 }}" {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
        <input type="hidden" class="form-control text-end gr_awal{{ $d->id_sortir }}" value="{{ $d->gr_awal ?? 0 }}">

    </td>
    @php
        $susut = empty($d->gr_akhir) ? 0 : (1 - $d->gr_akhir / $d->gr_awal) * 100;
    @endphp
    <td align="right">{{ number_format($susut, 0) }}%</td>
    <td align="right">{{ number_format($d->denda_sp ?? 0, 0) }}</td>
    <td align="right">{{ number_format($d->rp_target ?? 0, 0) }}</td>
    <td align="right">{{ number_format($d->ttl_rp ?? 0, 0) }}</td>
    <td>
        <select name="" id="" class="form-control bulan_dibayar{{ $d->id_sortir }}"
            {{ $d->selesai == 'Y' ? 'disabled' : '' }}>
            <option value="0">Pilih</option>
            @foreach (getListBulan() as $b)
                <option value="{{ $b->id_bulan }}" {{ $d->bulan == $b->id_bulan ? 'selected' : '' }}>
                    {{ $b->nm_bulan }}</option>
            @endforeach
        </select>
    </td>
    <td align="center">
        @if ($d->selesai == 'T')
            {{-- <a class="btn btn-primary btn-sm inputAkhir" href="#" gr_awal="{{ $d->gr_awal }}"
                gr_akhir="{{ $d->gr_akhir ?? 0 }}" no_box="{{ $d->no_box }}"
                id_anak="{{ $d->id_anak }}" href="#" data-bs-toggle="modal"
                data-bs-target="#inputAkhir"></i>Save</a> --}}

            <a href="#" class="btn btn-primary btn-sm save_akhir save_akhir{{ $d->id_sortir }}"
                id_sortir="{{ $d->id_sortir }}">save</a>

            <a href="#"
                {{ $d->id_anak == 0 || $d->id_kelas == 0 || empty($d->gr_akhir) || $d->bulan == 0 ? 'hidden' : '' }}
                class="btn btn-success btn-sm selesai_new selesai_new{{ $d->id_sortir }}"
                id_sortir="{{ $d->id_sortir }}">selesai</a>
        @else
            @php
                $posisi = auth()->user()->posisi_id;
            @endphp
            <a href="#" class="btn btn-danger btn-sm cancel_new cancel_new{{ $d->id_sortir }}"
                id_sortir="{{ $d->id_sortir }}"><i class="fas fa-redo"></i></a>
        @endif
    </td>
    <td align="center">
        @if ($d->selesai != 'T')
            <input type="checkbox" class="form-check cekTutup" name="cekTutup[]" id_sortir="{{ $d->id_sortir }}">
        @endif
    </td>

</tr>
