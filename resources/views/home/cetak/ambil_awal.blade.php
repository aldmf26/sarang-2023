<div class="row">
    <div class="col-lg-4">
        <button type="button" class="btn btn-sm btn-primary mb-3 btnKembaliTambahCetak"><i class="fas fa-arrow-left"></i>
            Kembali</button>
    </div>
    <div class="col-lg-12">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                /* font-size: 12px; */
                width: auto;
            }
        </style>
        <table class="table">
            <thead>
                <tr>
                    <th class="dhead">Pgws</th>
                    <th class="dhead">Tgl Terima</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Nama Anak</th>
                    <th class="dhead">Kelas/Paket</th>
                    <th class="dhead">Pcs Tidak ctk</th>
                    <th class="dhead">Gr Tidak ctk</th>
                    <th class="dhead">Pcs Awal</th>
                    <th class="dhead">Gr Awal</th>
                    <th class="dhead">Ttl Rp</th>
                    <th class="dhead">Selesai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cetak as $i => $c)
                    <tr>
                        <td>{{ $c->name }}</td>
                        <td>
                            <input type="hidden" name="id_cetak[]" value="{{ $c->id_cetak }}">
                            <input type="date" class="form-control" required name="tgl[]" required>
                        </td>
                        <td>
                            <select name="no_box[]" id="" class="select2-add pilihBox"
                                count="{{ $i + 1 }}" required>
                                <option value="">Pilih Box</option>
                                @foreach ($bk as $d)
                                    @if ($d->pcs_awal - $d->pcs_cabut > 1)
                                        <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }} ~ pcs
                                            {{ $d->pcs_awal - $d->pcs_cabut }} gr {{ $d->gr_awal - $d->gr_cabut }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td style="white-space: nowrap">{{ strtoupper($c->nama) }}
                            ({{ $c->id_kelas }})</td>
                        <td>
                            <select name="id_kelas_cetak[]" id="" class="select2-add pilihkelas"
                                count="{{ $i + 1 }}" required>
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $d)
                                    <option value="{{ $d->id_kelas_cetak }}">
                                        {{ ucwords($d->paket . ' ' . $d->kelas . '~' . ' Rp.' . $d->rp_pcs) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="text"
                                class="form-control text-end pcs_tdk_ctk{{ $i + 1 }} pcs_tdk_ctk"
                                name="pcs_tidak_ctk[]" count="{{ $i + 1 }}" value="0">
                        </td>
                        <td><input type="text" class="form-control text-end gr_tdk_ctk{{ $i + 1 }}"
                                name="gr_tidak_ctk[]" value="0">
                        </td>
                        <td>
                            <input type="hidden" class="rp_pcs{{ $i + 1 }}" name="rp_pcs[]">
                            <input type="text" class="form-control text-end pcs_awal{{ $i + 1 }} pcs_awal"
                                name="pcs_awal[]" count="{{ $i + 1 }}" value="0">
                        </td>
                        <td><input type="text" class="form-control text-end gr_awal{{ $i + 1 }}"
                                name="gr_awal[]" value="0">
                        </td>
                        <td><input type="text" class="form-control text-end total_rp{{ $i + 1 }}"
                                name="ttl_rp[]" readonly></td>
                        <td>
                            <button type="button" class="btn rounded-pill hapusCetakRow"
                                id_cetak="{{ $c->id_cetak }}" count="{{ $i + 1 }}"><i
                                    class="fas fa-trash text-danger"></i>
                            </button>
                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
