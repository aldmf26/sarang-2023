<table class="table table-bordered" id="tb_absen">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th class="text-center">Full</th>
            <th class="text-center">Setengah Hari</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($anak as $no => $a)

        @php
        $absen = DB::table('absen')
        ->where('id_anak', $a->id_anak)
        ->where('tgl', $tanggal)
        ->first();
        @endphp
        <tr>
            <td>{{$no+1}}</td>
            <td>{{$a->nama}}</td>

            @if (empty($absen->nilai))
            <td class="text-center">
                <a href="javascript:void(0)" class="btn save_absen btn-sm btn-secondary" ket="1" tgl="{{$tanggal}}"
                    id_anak="{{$a->id_anak}}">Full</a>
            </td>
            <td class="text-center">
                <a href="javascript:void(0)" class="btn save_absen btn-sm btn-secondary" ket="0.5"
                    id_anak="{{$a->id_anak}}" tgl="{{$tanggal}}">Setengah Hari</a>
            </td>
            @else
            <td class="text-center">
                <a href="javascript:void(0)"
                    class="btn  {{$absen->nilai == '1' ? 'delete_absen btn-sm btn-primary' : 'save_absen btn-sm btn-secondary' }} "
                    id_absen="{{$absen->id_absen}}" ket="1" id_anak="{{$a->id_anak}}" tgl="{{$tanggal}}">Full</a>
            </td>
            <td class="text-center">
                <a href="javascript:void(0)"
                    class="btn {{$absen->nilai == '0.5' ? 'delete_absen btn-sm btn-primary' : 'save_absen btn-sm btn-secondary' }}"
                    id_absen="{{$absen->id_absen}}" ket="0.5" id_anak="{{$a->id_anak}}" tgl="{{$tanggal}}">Setengah
                    Hari</a>
            </td>
            @endif

        </tr>
        @endforeach

    </tbody>

</table>