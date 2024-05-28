<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @media print {
            .form-check {
                display: none;
            }
        }
    </style>
</head>

<body class="py-3" x-data="{
    hide: true,
    classTh: 'align-middle text-center',
}">
    <h6 class="text-center"><u>FORMULIR SETOR BARANG KE SORTIR</u></h6>
    <div class="form-check float-end me-5">
        <input @change="hide = ! hide" class="form-check-input" type="checkbox" value="" id="cekbox">
        <label class="form-check-label" for="cekbox">
            Sembunyikan data
        </label>
        <button class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
    </div>
    <div class="px-3">
        <p>Pengawas :
            <span x-show="hide">{{ auth()->user()->find($detail[0]->id_pemberi)->name }} </span>
            <span>~</span>
            <span x-show="hide">{{ auth()->user()->find($detail[0]->id_penerima)->name }} </span>
        </p>
        <table style="font-size: 10px; border: 1px solid black" class="table table-bordered">

            <tr>
                <th :class="classTh" width="10%">Tgl</th>
                <th :class="classTh" width="8%">No Box</th>
                <th :class="classTh">Nama Anak</th>
                <th :class="classTh">Pcs Awal</th>
                <th :class="classTh">Gr Awal</th>
                <th :class="classTh">Pcs Akhir</th>
                <th :class="classTh">Gr Akhir</th>
                <th :class="classTh" width="3%">Sst %</th>
                <th :class="classTh" width="18%">Ttl Rp</th>
            </tr>
            @foreach ($detail as $d)
                <tr x-show="hide">
                    <td>{{ tglFormat($d->tanggal) }}</td>
                    <td>{{ $d->no_box }}</td>
                    <td></td>
                    <td>{{ $d->pcs_awal }}</td>
                    <td>{{ $d->gr_awal }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
            @for ($i = 0; $i < 25; $i++)
                <tr x-show="!hide">
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                    <td class="p-3"></td>
                </tr>
            @endfor
        </table>
    </div>


</body>

</html>
