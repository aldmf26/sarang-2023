<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">no box</th>
            <th class="dhead">tipe</th>
            <th class="dhead">ket</th>
            <th class="dhead">warna</th>
            <th class="dhead text-end">Pcs</th>
            <th class="dhead text-end">Gr</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($no_box as $key => $item)
            <input type="hidden" name="no_box[]" value="{{ $item }}">
            @php
                $bk = DB::table('bk')->where('no_box', $item)->where('kategori', 'cabut')->first();
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $bk->no_box }}</td>
                <td>{{ $bk->tipe }}</td>
                <td>{{ $bk->ket }}</td>
                <td>{{ $bk->warna }}</td>
                <td class="text-end">{{ $bk->pcs_awal }}</td>
                <td class="text-end">{{ $bk->gr_awal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
