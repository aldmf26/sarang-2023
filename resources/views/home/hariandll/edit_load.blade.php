<input type="hidden" name="id_hariandll" value="{{ $detail->id_hariandll }}">
<div class="row">
    <table class="table">
        <thead>
            <tr>
                <th class="dhead">Tanggal</th>
                <th class="dhead">Nama Anak</th>
                <th class="dhead">Nama Anak</th>
                <th class="dhead">Keterangan</th>
                <th class="dhead">Rupiah</th>
            </tr>
        </thead>
        <tbody>

            <tr class="baris">
                <td>
                    <input style="font-size: 13px;" type="date" value="{{ $detail->tgl }}" name="tgl"
                        class="form-control">
                </td>
                <td>
                    <select name="id_anak" class="form-control select2-edit" id="">
                        <option value="">- Pilih Anak -</option>
                        @foreach ($anak as $d)
                            <option {{ $d->id_anak == $detail->id_anak ? 'selected' : '' }} value="{{ $d->id_anak }}">
                                {{ strtoupper($d->nama) }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input value="{{ $detail->ket }}" type="text" name="ket" class="form-control">
                </td>
                <td>
                    <select name="lokasi" class="form-control select2-edit" id="">
                        <option value="">- Pilih Lokasi -</option>
                        @php
                            $lokasi = ['resto', 'aga', 'orchad', 'agrilaras'];
                        @endphp
                        @foreach ($lokasi as $d)
                            <option {{ $detail->lokasi == strtoupper($d) ? 'selected' : '' }}
                                value="{{ strtoupper($d) }}">
                                {{ strtoupper($d) }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" value="{{ $detail->rupiah }}" class="form-control text-end"
                        class="form-control" name="rupiah">
                </td>
             
            </tr>
        </tbody>

    </table>
</div>
