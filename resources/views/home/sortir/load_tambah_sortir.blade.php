@if (empty($datas))
    <h6 class="text-warning"><em>Data Kerja Sortir Tidak ada</em></h6>
@else
    <section class="row">
        <x-theme.alert pesan="{{ session()->get('error') }}" />
        <div class="col-lg-8">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="dhead">No Box</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead">Pgws</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="no_box" id="" required class="select3 pilihBox" count="1">
                                <option value="">Pilih Box</option>
                                @foreach ($boxBk as $d)
                                    @if ($d->gr_awal - $d->gr_cabut > 1)
                                        <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input readonly type="text" class="form-control text-end setPcs1">
                        </td>
                        <td>
                            <input readonly type="text" class="form-control text-end setGr1">
                        </td>
                        <td>
                            <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
                            <input type="hidden" class="form-control" name="id_pengawas" readonly
                                value="{{ auth()->user()->id }}">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="dhead" width="100">Nama Anak</th>
                        <th class="dhead" width="100">Tipe</th>
                        <th class="dhead">Tgl Terima</th>
                        <th class="dhead text-end" width="110">Pcs Awal</th>
                        <th class="dhead text-end" width="110">Pcuc</th>
                        <th class="dhead text-end" width="110">Gr Awal</th>
                        <th class="dhead text-end" width="130">Rp Target</th>
                        <th class="dhead">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $i => $d)
                        <input type="hidden" value="{{ $d->id_sortir }}" name="id_sortir[]">
                        <tr>
                            <td>
                                {{ strtoupper($d->nama) }} / {{ $d->id_kelas }}
                                <input type="hidden" name="id_anak[]" value="{{ $d->id_anak }}">
                                <input type="hidden" class="setHargaSatuan1">
                            </td>
                            @php
                                $kelas = DB::table('tb_kelas_sortir')
                                    ->orderBy('id_kelas', 'ASC')
                                    ->get();
                            @endphp
                            <td>
                                <select name="tipe[]" id="" class="form-control">
                                    @foreach ($kelas as $v)
                                        <option value="{{ $v->id_kelas }}" {{ $v->kelas == 'brg' ? 'selected' : '' }}>
                                            {{ strtoupper($v->kelas) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                    name="tgl_terima[]">
                            </td>
                            <td>
                                <input type="text" class="form-control text-end" value="0" id="pcsInput"
                                    name="pcs_awal[]">
                            </td>
                            <td>
                                <input type="text" class="form-control text-end" value="0" id="pcuc"
                                    name="pcuc[]">
                            </td>
                            <td>
                                <input type="text" class="form-control text-end setGr" count="{{ $i }}"
                                    value="0" id="grInput" name="gr_awal[]">
                            </td>
                            <td>
                                <input readonly type="text"
                                    class="form-control rupiahInput text-end setRupiah{{ $i }}"
                                    value="0" name="rupiah[]">
                            </td>
                            <td align="center">
                                <button type="button" class="btn rounded-pill hapusKerjSortir"
                                    id_sortir="{{ $d->id_sortir }}"><i class="fas fa-trash text-danger"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </section>
@endif
