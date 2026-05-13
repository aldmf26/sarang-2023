<div class="mb-2">
    <input type="text" id="tblinput1" class="form-control form-control-sm float-end" style="width: 200px"
        placeholder="🔍 Cari...">
    <div class="clearfix"></div>
</div>

<table class="table table-bordered" id="tablestr">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Nama Karyawan</th>
            <th class="dhead" width="100">Paket</th>
            <th class="text-end dhead">Pcs Awal</th>
            <th class="text-end dhead">Gr Awal</th>
            <th width="70px" class="text-end dhead">Pcs Tdk Sortir</th>
            <th width="70px" class="text-end dhead">Gr Tdk Sortir</th>
            <th width="70px" class="text-end dhead">Pcs Akhir</th>
            <th width="120px" class="text-end dhead">Gr Akhir</th>
            <th class="text-end dhead">Susut%</th>
            <th class="text-end dhead">Denda</th>
            <th class="text-end dhead">Rp Target</th>
            <th class="text-end dhead">Total Rp</th>
            <th class="text-end dhead">Bulan Dibayar</th>
            <th class="dhead" width="100">Aksi</th>
            <th class="dhead" width="70">
                <center>
                    @if ($adaDitutup)
                        <input style="text-align: center" type="checkbox" class="form-check" id="cekSemuaTutup">
                    @endif
                    <br>
                    <span class="badge bg-danger btn_tutup d-none" tipe="tutup" style="cursor: pointer">
                        <i class="fas fa-check"></i> Tutup
                    </span>
                </center>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            @php
                $gr_awal_hitung = $d->gr_awal - $d->gr_tdk_sortir;
                $susut = empty($d->gr_akhir) || $gr_awal_hitung == 0 ? 0 : (1 - $d->gr_akhir / $gr_awal_hitung) * 100;
            @endphp
            <tr data-id="{{ $d->id_sortir }}">

                {{-- No --}}
                <td>{{ $no + 1 }}</td>

                {{-- Tanggal --}}
                <td>
                    <input type="date" value="{{ $d->tgl }}" class="form-control tgl{{ $d->id_sortir }}"
                        name="tgl_cabut[]">
                </td>

                {{-- No Box --}}
                <td>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
                        id_sortir="{{ $d->id_sortir }}">
                        {{ $d->no_box }}
                    </a>
                </td>

                {{-- Nama Karyawan --}}
                <td>
                    @if ($d->selesai == 'Y')
                        {{ $d->nama }}
                    @else
                        <select name="id_anak[]" class="select2_add id_anak{{ $d->id_sortir }}">
                            <option value="">Pilih Anak</option>
                            @foreach ($anak as $a)
                                <option value="{{ $a->id_anak }}" {{ $a->id_anak == $d->id_anak ? 'selected' : '' }}>
                                    {{ ucwords($a->nama) }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </td>

                {{-- Paket / Kelas --}}
                <td>
                    <select class="form-control id_kelas{{ $d->id_sortir }}"
                        {{ $d->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih</option>
                        @foreach ($kelas as $v)
                            <option value="{{ $v->id_kelas }}" {{ $d->id_kelas == $v->id_kelas ? 'selected' : '' }}>
                                {{ strtoupper($v->kelas) }}
                            </option>
                        @endforeach
                    </select>
                </td>

                {{-- Pcs Awal --}}
                <td align="right">{{ $d->pcs_awal ?? 0 }}</td>

                {{-- Gr Awal --}}
                <td align="right">{{ $d->gr_awal ?? 0 }}</td>

                {{-- Pcs Tdk Sortir --}}
                <td>
                    <input type="text" class="form-control text-end pcs_tidak pcs_tdk_sortir{{ $d->id_sortir }}"
                        value="{{ $d->pcs_tdk_sortir }}" count="{{ $d->id_sortir }}"
                        {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
                </td>

                {{-- Gr Tdk Sortir --}}
                <td>
                    <input type="text" class="form-control text-end gr_tdk_sortir{{ $d->id_sortir }}"
                        value="{{ $d->gr_tdk_sortir }}" {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
                </td>

                {{-- Pcs Akhir --}}
                <td align="right">
                    <input type="hidden" class="no{{ $d->id_sortir }}" value="{{ $no + 1 }}">
                    <input type="hidden" class="pcs_awal{{ $d->id_sortir }}" value="{{ $d->pcs_awal ?? 0 }}">
                    <input type="text" class="form-control text-end pcs_akhir{{ $d->id_sortir }}"
                        value="{{ $d->pcs_akhir == 0 ? $d->pcs_awal : $d->pcs_akhir }}" readonly>
                </td>

                {{-- Gr Akhir --}}
                <td align="right">
                    <input type="text" class="form-control text-end gr_akhir{{ $d->id_sortir }}"
                        value="{{ $d->gr_akhir ?? 0 }}" {{ $d->selesai == 'Y' ? 'readonly' : '' }}>
                    <input type="hidden" class="gr_awal{{ $d->id_sortir }}" value="{{ $d->gr_awal ?? 0 }}">
                </td>

                {{-- Susut% --}}
                <td align="right">{{ number_format($susut, 0) }}%</td>

                {{-- Denda --}}
                <td align="right">{{ number_format($d->denda_sp ?? 0, 0) }}</td>

                {{-- Rp Target --}}
                <td align="right">{{ number_format($d->rp_target ?? 0, 0) }}</td>

                {{-- Total Rp --}}
                <td align="right">{{ number_format($d->ttl_rp ?? 0, 0) }}</td>

                {{-- Bulan Dibayar --}}
                <td>
                    <select class="form-control bulan_dibayar{{ $d->id_sortir }}"
                        {{ $d->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih</option>
                        @foreach ($list_bulan as $b)
                            <option value="{{ $b->id_bulan }}" {{ $d->bulan == $b->id_bulan ? 'selected' : '' }}>
                                {{ $b->nm_bulan }}
                            </option>
                        @endforeach
                    </select>
                </td>

                {{-- Aksi --}}
                <td align="center">
                    @if ($d->selesai == 'T')
                        <a href="#" class="btn btn-primary btn-sm save_akhir save_akhir{{ $d->id_sortir }}"
                            id_sortir="{{ $d->id_sortir }}">
                            Save
                        </a>
                        <a href="#" class="btn btn-success btn-sm selesai_new selesai_new{{ $d->id_sortir }}"
                            id_sortir="{{ $d->id_sortir }}"
                            {{ $d->id_anak == 0 || $d->id_kelas == 0 || empty($d->gr_akhir) || $d->bulan == 0 ? 'hidden' : '' }}>
                            Selesai
                        </a>
                    @else
                        @if (empty($d->no_box_formulir))
                            <a href="#" class="btn btn-danger btn-sm cancel_new cancel_new{{ $d->id_sortir }}"
                                id_sortir="{{ $d->id_sortir }}">
                                <i class="fas fa-redo"></i>
                            </a>
                        @endif
                    @endif
                </td>

                {{-- Cek Tutup --}}
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
