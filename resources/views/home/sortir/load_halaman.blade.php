<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Nama Karyawan</th>
            <th class="dhead" width="70">Paket</th>
            <th class="text-end dhead">Pcs Awal</th>
            <th class="text-end dhead">Gr Awal</th>
            <th width="70px" class="text-end dhead">Pcs Akhir</th>
            <th width="70px" class="text-end dhead">Gr Akhir</th>
            <th class="text-end dhead">Susut%</th>
            <th class="text-end dhead">Denda</th>
            <th class="text-end dhead">Rp Target</th>
            <th class="text-end dhead">Total Rp</th>
            <th class="text-end dhead">Bulan Dibayar</th>
            <th class="dhead" width="100">Aksi</th>
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
                    {{-- <span class="badge bg-danger btn_tutup d-none mt-3" tipe="cancel_data"
                        style="cursor: pointer">Cancel</span> --}}

                </center>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            <tr data-id="{{ $d->id_sortir }}">
                <td>{{ $no + 1 }}</td>
                <td>{{ date('d M Y', strtotime($d->tgl)) }}</td>
                <td>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
                        id_sortir="{{ $d->id_sortir }}">{{ $d->no_box }}</a>
                </td>
                <td>
                    <select name="id_anak[]" id="" class="select2_add id_anak{{ $d->id_sortir }}"
                        {{ $d->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih Anak</option>
                        @foreach ($anak as $a)
                            <option value="{{ $a->id_anak }}" {{ $a->id_anak == $d->id_anak ? 'selected' : '' }}>
                                {{ ucwords($a->nama) }}</option>
                        @endforeach
                    </select>
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
                    <input type="hidden" class="no{{ $d->id_sortir }}" value="{{ $no + 1 }}">
                    <input type="hidden" class="form-control text-end pcs_awal{{ $d->id_sortir }}"
                        value="{{ $d->pcs_awal ?? 0 }}">
                    <input type="text" class="form-control text-end pcs_akhir{{ $d->id_sortir }}"
                        value="{{ $d->pcs_akhir ?? 0 }}" {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
                </td>
                <td align="right">

                    <input type="text" class="form-control text-end gr_akhir{{ $d->id_sortir }}"
                        value="{{ $d->gr_akhir ?? 0 }}" {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
                    <input type="hidden" class="form-control text-end gr_awal{{ $d->id_sortir }}"
                        value="{{ $d->gr_awal ?? 0 }}">

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
                        <option value="">Pilih</option>
                        @foreach ($bulan as $b)
                            <option value="{{ $b->id_bulan }}" {{ $d->bulan == $b->id_bulan ? 'selected' : '' }}>
                                {{ $b->bulan }}</option>
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
                            {{ $d->id_anak == 0 || $d->id_kelas == 0 || empty($d->pcs_akhir) || empty($d->gr_akhir) || $d->bulan == 0 ? 'hidden' : '' }}
                            class="btn btn-success btn-sm selesai_new selesai_new{{ $d->id_sortir }}"
                            id_sortir="{{ $d->id_sortir }}">selesai</a>
                    @else
                        @php
                            $posisi = auth()->user()->posisi_id;
                        @endphp
                        <a href="#" {{ $posisi == 1 ? '' : 'hidden' }}
                            class="btn btn-danger btn-sm cancel_new cancel_new{{ $d->id_sortir }}"
                            id_sortir="{{ $d->id_sortir }}"><i class="fas fa-redo"></i></a>
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
