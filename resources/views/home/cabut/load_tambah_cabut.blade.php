<section class="row">
    <div class="col-lg-4">
        <button type="button" class="btn btn-sm btn-primary mb-3 btnKembaliTambahCabut"><i class="fas fa-arrow-left"></i> Kembali</button>
    </div>
    <div class="col-lg-12">
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead" width="100">Pgws</th>
                    <th class="dhead" width="100">Tgl Terima</th>
                    <th class="dhead" width="90">No Box</th>
                    <th class="dhead" width="150">Nama Anak</th>
                    <th class="dhead">Kelas</th>
                    <th class="dhead text-end" width="110">Pcs Awal</th>
                    <th class="dhead text-end" width="110">Gr Awal</th>
                    <th class="dhead text-end" width="130">Ttl Rp</th>
                    <th class="dhead">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($getAnak as $i => $x)
                    <tr>
                        <td>
                            <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
                            <input type="hidden" class="form-control" name="id_pengawas[]" readonly
                                value="{{ auth()->user()->id }}">
                        </td>
                        <td>
                            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
                        </td>
                        <td>
                            <select name="no_box[]" id="" class="select3 pilihBox" count="{{ $i+1 }}">
                                <option value="">Pilih Box</option>
                                @foreach ($boxBk as $d)
                                    @if ($d->pcs_awal - $d->pcs_cabut > 1)
                                        <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <span class="h6">{{ strtoupper($x->nama) }} <span
                                    class="float-end">({{ $x->kelas }})</span></span>
                                    <input type="hidden" name="id_anak[]" value="{{ $x->id_anak }}">
                            {{-- <select name="id_anak[]" id="" class="select3 pilihAnak" count="1">
                            <option value="">Pilih Anak</option>
                            @foreach ($anak as $a)
                            <option {{$d->id_anak == $a->id_anak ? 'selected' : ''}} data-kelas="{{ $a->kelas }}" value="{{ $a->id_anak }}">
                                ({{ $a->kelas }}) {{ ucwords($a->nama) }}</option>
                            @endforeach
                        </select> --}}
                            <input type="hidden" class="setHargaSatuan{{$i+1}}">
                        </td>
                        <td>
                            @php
                                $kelasSelect = DB::table('tb_kelas')
                                    ->where('kelas', $x->kelas)
                                    ->get();
                            @endphp
                            <select name="kelas_tipe[]" id="" class="select3 pilihAnak" count="{{$i+1}}">
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelasSelect as $d)
                                        <option value="{{ $d->id_kelas }}">{{ ucwords("$d->kelas $d->tipe ($d->gr gr) " . number_format($d->rupiah,0)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control text-end setPcs{{$i+1}}" value="0" id="pcsInput"
                                name="pcs_awal[]">
                        </td>
                        <td>
                            <input type="text" class="form-control text-end setGr setGr{{$i+1}}" count="{{$i+1}}"
                                value="0" id="grInput" name="gr_awal[]">
                        </td>
                        <td>
                            <input readonly type="text" class="form-control rupiahInput text-end setRupiah{{$i+1}}"
                                value="0">
                            <input readonly type="hidden" class="form-control rupiahInput text-end rupiahBiasa{{$i+1}}"
                                value="0" name="rupiah[]">
                        </td>
                        <td align="center">
                            <button type="button" class="btn rounded-pill hapusCabutRow" id_cabut="{{ $x->id_cabut }}" id_anak="{{ $x->id_anak }}" count="{{ $i+1 }}"><i
                                    class="fas fa-trash text-danger"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tbody id="tbh_baris">
            </tbody>
            {{-- <tfoot>
                <tr>
                    <th colspan="9">
                        <button type="button" class="btn btn-block btn-lg tbh_baris"
                            style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                            <i class="fas fa-plus"></i> Tambah Baris Baru
                        </button>
                    </th>
                </tr>
            </tfoot> --}}
        </table>
        {{-- <button class="btn btn-primary btn-md saveCabut" type="button">Simpan</button> --}}
    </div>
</section>
