<table class="table table-bordered" id="tbl3">
    <thead>
        <tr>
            <th class="dhead">No</th>
            <th class="dhead">No Box</th>
            <th class="dhead text-center">Grade 1</th>
            <th class="dhead text-end">Pcs 1</th>
            <th class="dhead text-end">Gr 1</th>

            <th width="120" class="bg-info text-white text-center">Grade 2</th>
            <th width="120" class="bg-info text-white text-end">Pcs 2</th>
            <th width="120" class="bg-info text-white text-end">Gr 2</th>
            <th width="120" class="bg-info text-white">No Barcode Pengiriman</th>

            <th class="dhead text-end">Pcs Kirim</th>
            <th class="dhead text-end">Gr Kirim air %</th>
            <th width="71" class="dhead text-end">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pengiriman as $i => $d)
            <tr x-data="{
                gr2: {{ $d->gr }},
            }">
                <td>{{ $i + 1 }} </td>
                <td>P{{ $d->no_box }}</td>
                <td align="center">{{ $d->grade }}</td>
                <td align="right">{{ $d->pcs1 }}</td>
                <td align="right">{{ $d->gr1 }}</td>

                <td align="center">
                    <input onclick="$(this).select()" name="grade2[]" required value="{{ $d->grade }}"
                        type="text" class="form-control text-primary">
                    <input name="box_grading[]" required value="{{ $d->no_box }}" type="hidden"
                        class="form-control text-primary">
                </td>
                <td align="center">
                    <input onclick="$(this).select()" name="pcs2[]" required value="{{ $d->pcs }}" type="text"
                        class="text-end form-control text-primary">
                </td>
                <td align="center">
                    <input name="id_pengiriman[]" required value="{{ $d->id_pengiriman }}" type="hidden">
                    <input onclick="$(this).select()" name="gr2[]" x-model="gr2" required
                        value="{{ $d->gr }}" type="text" class="text-end form-control text-primary">
                </td>
                <td align="center">
                    <input onclick="$(this).select()" name="barcode[]" value="{{ $d->no_barcode }}"
                        placeholder="cth: 10001" autocomplete="off" type="text" class="form-control text-primary">
                </td>

                <td align="right">
                    {{ $d->pcs }}
                </td>
                <td align="right" x-text="(Number(gr2) / Number(kadar)) + Number(gr2)">-
                </td>
                <td align="center">
                    <span onclick="hapus({{ $d->id_pengiriman }})" class="pointer badge bg-danger"><i
                        class="fas fa-trash"></i></span>
                    <span onclick="ubah(this)" class="pointer badge bg-info"><i
                        class="fas fa-pen"></i></span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
