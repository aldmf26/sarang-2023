<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Export Absen Detail.xlsx"');
header('Cache-Control: max-age=0');
?>
<table id="tblAldi" style="border:1px solid #435EBE;" class="table table-bordered table-hover table-striped">
    <thead style="border:1px solid black;">
        <tr id="sticky-header">
            <th class="dhead">Pgws</th>
            <th class="dhead">Anak</th>
            @for ($i = 1; $i <= $jumlahHari; $i++)
                <th class="dhead">{{ $i }}</th>
            @endfor
            <th class="dhead">Total</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($absen as $d)
            <tr>
                <td class="sticky-cell dhead text-white">{{ $d->name }}</td>
                <td class="sticky-cell dhead text-white">{{ $d->nama }}</td>
                @php
                    $ttl = 0;
                @endphp
                @for ($i = 1; $i <= $jumlahHari; $i++)
                    @php
                        $getTgl = DB::table('absen')
                            ->where([['id_anak', $d->id_anak], ['tgl', "$tahunGet-$bulanGet-$i"]])
                            ->count();
                        $ttl += $getTgl ?? 0;
                    @endphp
                    <td class="text-center">{{ empty($getTgl) ? '-' : $getTgl }}</td>
                @endfor
                <td class="text-center">{{ $ttl }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
