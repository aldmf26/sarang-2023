<div class="row">
    <input type="hidden" name="id_anak" value="{{ $detail->id_anak }}">
    <input type="hidden" name="no_box" value="{{ $detail->no_box }}">
    <div class="col-lg-9">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead"  width="100">Nama Anak</th>
                    <th class="dhead">Tgl Terima</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead text-end">Pcs Awal</th>
                    <th class="dhead text-end">Gr Awal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fs-bold">{{ ucwords($detail->nama) }}</td>
                    <td>
                        <input type="date" readonly value="{{ $detail->tgl_terima }}" class="form-control">
                    </td>
                    <td>
                        <input type="text" readonly value="{{ $detail->no_box }}" class="form-control">
                    </td>
                    <td>
                        <input readonly value="{{ $detail->pcs_awal }}" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input readonly value="{{ $detail->gr_awal }}" type="text" class="form-control text-end">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead text-end">Pcs Akhir</th>
                    <th class="dhead text-end">Gr Akhir</th>
                    <th class="dhead text-end">Pcs Hcr</th>
                    <th class="dhead text-end">EOT</th>
                    <th class="dhead text-end">Susut</th>
                    <th class="dhead text-end">Ttl Gaji</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input value="{{ $detail->pcs_akhir ?? 0}}" required name="pcs_akhir" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input value="{{ $detail->gr_akhir ?? 0}}" required name="gr_akhir" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input value="{{ $detail->pcs_hcr ?? 0}}" name="pcs_hcr" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input value="{{ $detail->eot ?? 0}}" name="eot" type="text" class="form-control text-end">
                    </td>
                    <td>
                        @php
                            $susut = empty($detail->gr_akhir) ? 0 : ((1 - ($detail->gr_flx + $detail->gr_akhir) / $detail->gr_awal) * 100)
                        @endphp
                        <input name="susut" value="{{ number_format($susut, 0) }}%" type="text" class="form-control text-end" readonly>
                    </td>
                    <td>
                        <input name="ttl_gaji" value="" type="text" class="form-control text-end" readonly>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>