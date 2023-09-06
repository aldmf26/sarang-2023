<div class="row">
    <input type="hidden" name="id_anak" value="{{ $detail->id_anak }}">
    <input type="hidden" name="no_box" value="{{ $detail->no_box }}">
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead"  width="100">Nama Anak</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead text-end">Pcs Awal</th>
                    <th class="dhead text-end">Gr Awal</th>
                    <th class="dhead text-end">Pcs Akhir</th>
                    <th class="dhead text-end">Gr Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fs-bold">{{ strtoupper($detail->nama) }}</td>
                
                    <td>
                        <input type="text" readonly value="{{ $detail->no_box }}" class="form-control">
                    </td>
                    <td>
                        <input readonly value="{{ $detail->pcs_awal }}" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input readonly value="{{ $detail->gr_awal }}" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input value="{{ $detail->pcs_akhir ?? 0}}" required name="pcs_akhir" type="text" class="form-control text-end">
                    </td>
                    <td>
                        <input value="{{ $detail->gr_akhir ?? 0}}" required name="gr_akhir" type="text" class="form-control text-end">
                    </td>
               
                </tr>
            </tbody>
        </table>
    </div>
</div>