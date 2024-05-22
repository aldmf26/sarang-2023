<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Nama anak</th>
            <th class="dhead text-end">Pcs</th>
            <th class="dhead text-end">Gr</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($id_cabut as $key => $item)
            <input type="hidden" name="id_cabut[]" value="{{ $item }}">
            @php
                $cabut = DB::table('cabut')
                    ->leftJoin('tb_anak', 'cabut.id_anak', '=', 'tb_anak.id_anak')
                    ->where('id_cabut', $item)
                    ->first();
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $cabut->no_box }}</td>
                <td>{{ $cabut->nama }}</td>
                <td class="text-end">{{ $cabut->pcs_akhir }}</td>
                <td class="text-end">{{ $cabut->gr_akhir }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
