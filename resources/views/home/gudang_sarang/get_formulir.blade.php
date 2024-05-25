<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead text-end">Pcs</th>
            <th class="dhead text-end">Gr</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($no_box as $key => $item)
            <input type="hidden" name="no_box[]" value="{{ $item }}">
            @php
                $cabut = DB::selectOne(
                    "SELECT b.name, a.no_box, sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir 
                    FROM cabut as a 
                    left join users as b on b.id = a.id_pengawas
                    where a.no_box = '$item'",
                );
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $cabut->no_box }}</td>
                <td>{{ $cabut->name }}</td>
                <td class="text-end">{{ $cabut->pcs_akhir }}</td>
                <td class="text-end">{{ $cabut->gr_akhir }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
