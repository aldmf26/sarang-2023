<table class="table table-bordered" id="cbt_selesai">
    <thead>
        <tr>
            <th>#</th>
            <th>No Box</th>
            <th>Pengawas</th>
            <th>Nama Anak</th>
            <th class="text-end">Pcs Akhir</th>
            <th class="text-end">Gr Akhir</th>
            <th class="text-center">
                <input type="checkbox" name="" id="checkAll">
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->pengawas }}</td>
                <td>{{ $d->nama }}</td>
                <td class="text-end">{{ $d->pcs_akhir }}</td>
                <td class="text-end">{{ $d->gr_akhir }}</td>
                <td class="text-center">
                    @if ($d->selesai == 'Y')
                        <input type="checkbox" no_box="{{ $d->no_box }}" class="checkbox" name=""
                            id="" value="{{ $d->no_box }}">
                    @else
                        belum selesai
                    @endif

                </td>
            </tr>
        @endforeach
    </tbody>
</table>
