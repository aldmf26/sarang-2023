<div class="row">
    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000000;
            line-height: 36px;
            font-size: 12px;
            width: auto;
        }

        .form-control {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-clip: padding-box;
            background-color: #fff;
            border: 1px solid #dce7f1;
            border-radius: 0.25rem;
            color: #607080;
            display: block;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.5;
            padding: 0.375rem 0.75rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            width: 100%;
            border-radius: 5px !important;
        }
    </style>
    <div class="col-lg-12">
        <table class="table " style="font-size: 12px">
            <thead>
                <tr>
                    <th class="dhead" width="80">Tgl Terima</th>
                    <th class="dhead" width="80">Nama Anak</th>
                    <th class="dhead" width="80">No Box</th>
                    <th class="dhead text-end"width="60">Pcs Awal</th>
                    <th class="dhead text-end" width="60">Gr Awal</th>
                    <th class="dhead text-end" width="80">Pcs Akhir</th>
                    <th class="dhead text-end" width="80">Gr Akhir</th>
                    <th class="dhead text-end" width="80">Gr Flex</th>
                    <th class="dhead text-end" width="80">EOT</th>
                    <th class="dhead text-end" width="80">Pcsx bayar</th>
                    <th class="dhead text-end" width="100">Ttl Rp</th>
                    <th class="dhead text-end" width="80">Dibayar</th>
                    <th class="dhead text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cabut_spesial as $no => $detail)
                    <tr data-id="{{ $detail->id_cabut_spesial }}">
                        <td><input type="date" class="form-control" name="tgl_terima"
                                value="{{ empty($detail->tgl_terima) ? date('Y-m-d') : $detail->tgl_terima }}"></td>
                        <td class="fs-bold">{{ strtoupper($detail->nama) }}</td>
                        <td>
                            <input type="text" readonly value="{{ $detail->no_box }}" class="form-control">
                            <input type="hidden" readonly value="{{ $detail->id_cabut_spesial }}"
                                name="id_cabut_spesial[]" class="form-control">
                        </td>
                        <td>
                            <input readonly value="{{ $detail->pcs_awal }}" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input readonly value="{{ $detail->gr_awal }}" type="text" class="form-control text-end">
                        </td>
                        <td>
                            <input value="{{ $detail->pcs_akhir ?? 0 }}" required name="pcs_akhir" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input value="{{ $detail->gr_akhir ?? 0 }}" required name="gr_akhir" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input value="{{ $detail->gr_flex ?? 0 }}" required name="gr_flex" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input value="{{ $detail->eot ?? 0 }}" name="eot" type="text"
                                class="form-control text-end">

                            <input value="{{ $detail->ttl_rp ?? 0 }}" type="hidden" name="ttl_rp"
                                class="form-control text-end rp_target{{ $detail->id_cabut_spesial }}">
                        </td>
                        <td>
                            <input value="{{ $detail->pcs_hcr ?? 0 }}" name="pcs_hcr" type="text"
                                class="form-control text-end pcs_hcr" count="{{ $detail->id_cabut_spesial }}">
                        </td>
                        <td class="text-end fw-bold ttl_rp{{ $detail->id_cabut_spesial }}">Rp
                            {{ number_format($detail->ttl_rp, 2) }}</td>
                        <td>
                            <select name="bulan_dibayar" id="" class="select3">
                                <option value="">Pilih Bulan</option>
                                @foreach ($bulan as $b)
                                    <option value="{{ $b->bulan }}"
                                        {{ $b->bulan == $detail->bulan_dibayar ? 'selected' : '' }}>
                                        {{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td align="center" style="white-space: nowrap">
                            <button type="button" class="btn btn-sm btn-primary btn_simpan">Simpan</button>
                            <button type="button" data-bs-target="#selesai" data-bs-toggle="modal"
                                id_cabut="{{ $detail->id_cabut_spesial }}" class="btn btn-sm btn-success selesai"
                                {{ empty($detail->eot) || empty($detail->pcs_akhir) || empty($detail->gr_akhir) ? 'hidden' : '' }}>Selesai</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
