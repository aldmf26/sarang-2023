<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>{{ $title }}</title>

    <style>
        @media print {
            .print_hilang {
                display: none;
            }
        }
    </style>
</head>

<body>
    <br>
    <div class="container-fluid">
        <div class="d-flex justify-content-between print_hilang">
            <a href="{{ route('gudangsarang.gudang_cbt_selesai') }}" class="print_hilang btn btn-sm btn-warning"><i
                    class="fa-solid fa-left-long"></i>
                Kembali</a>
            <a onclick="window.print()" href="#" class="print print_hilang btn btn-sm btn-primary"><i
                    class="fa-solid fa-print"></i>
                Print</a>
        </div>
        <h5 class="fw-bold text-center" style="text-decoration: underline">PO CABUT : {{ $no_invoice }}</h5>

        <h6 class="fw-bold">Pengawas : {{ $ket_formulir->name }} ~ {{ $ket_formulir->penerima }} | Tanggal :
            {{ tanggal($ket_formulir->tanggal) }}</h6>

        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" style="font-size: 13px">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Nama Partai</th>
                            <th>No Box</th>
                            <th>Tipe</th>
                            <th>Ket</th>
                            <th>Warna</th>
                            <th class="text-end">Pcs Awal</th>
                            <th class="text-end">Gr Awal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $ttlPcs = 0;
                            $ttlGr = 0;
                        @endphp
                        @foreach ($formulir as $no => $f)
                            @php
                                $ttlPcs += $f->pcs_awal;
                                $ttlGr += $f->gr_awal;
                            @endphp
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td></td>
                                <td>{{ $f->nm_partai }}</td>
                                <td>{{ $f->no_box }}</td>
                                <td>{{ $f->tipe }}</td>
                                <td>{{ $f->ket }}</td>
                                <td>{{ $f->warna }}</td>
                                <td class="text-end">{{ $f->pcs_awal }}</td>
                                <td class="text-end">{{ $f->gr_awal }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-center" colspan="7">Total</th>
                            <th class="text-end">{{ number_format($ttlPcs, 0) }}</th>
                            <th class="text-end">{{ number_format($ttlGr, 0) }}</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>


</body>

</html>
