</html>
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

            .section {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <br>
    <div class="container-fluid">
        <div class="d-flex justify-content-between print_hilang">
            {{-- <a href="{{ route('gudangsarang.gudang_cbt_selesai') }}" class="print_hilang btn btn-sm btn-warning"><i
                    class="fa-solid fa-left-long"></i>
                Kembali</a> --}}
            <a href=""></a>
            <a onclick="window.print()" href="#" class="print print_hilang btn btn-sm btn-primary float-end"><i
                    class="fa-solid fa-print"></i>
                Print</a>
        </div>
        @foreach ($halaman as $h)
            @php
                $detail = DB::table('formulir_sarang')
                    ->leftJoin('users', 'users.id', 'formulir_sarang.id_penerima')
                    ->where('no_invoice', $no_invoice)
                    ->where('kategori', 'sortir')
                    ->where('id_penerima', $h->id_penerima)
                    ->get();
            @endphp
            <div class="section">
                <h5 class="fw-bold text-center" style="text-decoration: underline">PO SORTIR : {{ $no_invoice }}</h5>

                <h6 class="fw-bold">Pengawascwq : {{ auth()->user()->find($h->id_pemberi)->name }} ~
                    {{ $h->name }} </h6>
                <div class="row">
                    <div class="col-lg-12">

                        <table class="table table-bordered" style="font-size: 13px; border:1px solid black">
                            <tr>
                                <th>Tgl</th>
                                <th>No Box</th>
                                <th>Nama Anak</th>
                                <th class="text-end">Pcs Awal</th>
                                <th class="text-end"> Gr Awal</th>
                                <th class="text-end">Pcs Akhir</th>
                                <th class="text-end">Gr Akhir</th>
                                <th class="text-end">Susut %</th>
                                <th class="text-end">Total Rp</th>
                            </tr>
                            @foreach ($detail as $d)
                                <tr>
                                    <td style="width: 100px"></td>
                                    <td>{{ $d->no_box }}</td>
                                    <td></td>
                                    <td class="text-end">{{ $d->pcs_awal }}</td>
                                    <td class="text-end">{{ $d->gr_awal }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endforeach


    </div>


</body>

</html>
