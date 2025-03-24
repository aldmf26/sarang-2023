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
            <div class="section">
                <h5 class="fw-bold text-center" style="text-decoration: underline">PO SORTIR : {{ $no_invoice }}</h5>

                <h6 class="fw-bold">Pengawas : {{ auth()->user()->find($h->id_pemberi)->name }} ~
                    {{ $h->name }} </h6>
                <div class="row">
                    <div class="col-lg-12">

                        <table class="table table-bordered" style="font-size: 13px; border:1px solid black">
                            <thead>
                                <tr class="align-middle">
                                    <th>Tgl</th>
                                    <th width="80">Nama Partai</th>
                                    <th width="70">No Box</th>
                                    <th width="89">Grade</th>
                                    <th width="200">Nama Anak</th>
                                    <th width="60" class="text-end">Pcs Awal Ctk</th>
                                    <th width="60" class="text-end">Gr Awal Ctk</th>
                                    <th width="60" class="text-end">Pcs Awal</th>
                                    <th width="60" class="text-end">Gr Awal</th>
                                    <th width="60" class="text-end">Sst Program</th>
                                    <th width="60" class="text-end">Sst Aktual</th>
                                    <th width="60" class="text-end">Pcs Akhir</th>
                                    <th width="60" class="text-end">Gr Akhir</th>
                                    <th width="60" class="text-end">Susut %</th>
                                    <th width="130" class="text-end">Total Rp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $detail = DB::table('formulir_sarang as a')
                                        ->leftJoin('users', 'users.id', '=', 'a.id_penerima')
                                        ->leftJoin('bk as b', function ($join) {
                                            $join->on('b.no_box', '=', 'a.no_box')->where('b.kategori', '=', 'cabut');
                                        })
                                        ->leftJoin('cetak_new as c', 'c.no_box', '=', 'a.no_box')
                                        ->where('a.no_invoice', $no_invoice)
                                        ->where('a.kategori', 'sortir')
                                        ->where('a.id_penerima', $h->id_penerima)
                                        ->selectRaw(
                                            'b.nm_partai, b.tipe as tipe, b.ket, a.no_box, a.pcs_awal, a.gr_awal,c.pcs_awal_ctk as pcs_cbt, c.gr_awal_ctk as gr_cbt',
                                        )
                                        ->get()
                                        ->toArray();
                                @endphp
                                @foreach ($detail as $d)
                                    <tr>
                                        <td style="width: 100px"></td>
                                        <td>{{ $d->nm_partai }}</td>
                                        <td>{{ $d->no_box }}</td>
                                        <td>{{ $d->tipe . ' - ' . $d->ket }}</td>
                                        <td></td>
                                        <td class="text-end">{{ $d->pcs_cbt }}</td>
                                        <td class="text-end">{{ $d->gr_cbt }}</td>
                                        <td class="text-end">{{ $d->pcs_awal }}</td>
                                        <td class="text-end">{{ $d->gr_awal }}</td>
                                        <td class="text-end">{{ $d->gr_cbt - $d->gr_awal }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total</th>
                                    <th class="text-end">{{ number_format(sumCol($detail, 'pcs_cbt'), 0) }}</th>
                                    <th class="text-end">{{ number_format(sumCol($detail, 'gr_cbt'), 0) }}</th>
                                    <th class="text-end">{{ number_format(sumCol($detail, 'pcs_awal'), 0) }}</th>
                                    <th class="text-end">{{ number_format(sumCol($detail, 'gr_awal'), 0) }}</th>
                                    <th class="text-end">{{ number_format(sumCol($detail, 'gr_cbt') - sumCol($detail, 'gr_awal'), 0) }}</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        @endforeach


    </div>


</body>

</html>
