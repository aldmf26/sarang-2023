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
    <form action="{{ route('gudangsarang.gethancuran.savePthSortir') }}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between print_hilang">
                <a href="{{ route('gudangsarang.invoice_grade', ['kategori' => 'grade']) }}"
                    class="print_hilang btn btn-sm btn-warning"><i class="fa-solid fa-left-long"></i>
                    Kembali</a>
                <a href=""></a>
                <button type="submit" class="print print_hilang btn btn-sm btn-primary float-end"><i
                        class="fa-solid fa-print"></i>
                    Save</button>
            </div>

            <div class="section">
                <br>
                <br>
                <h5 class="fw-bold text-center" style="text-decoration: underline">{{ $title }} :
                    {{ $no_invoice }}
                </h5>

                <h6 class="fw-bold">Pengawas : {{ auth()->user()->find($formulir[0]->id_pemberi)->name }} ~
                    {{ auth()->user()->find($formulir[0]->id_penerima)->name }} | Tanggal :
                    {{ tanggal($formulir[0]->tanggal) }}</h6>
                <div class="row">
                    <input type="hidden" name="no_invoice" value="{{ $no_invoice }}">
                    <div class="col-lg-12">

                        <table class="table table-bordered" style="font-size: 13px; border:1px solid black">
                            <thead>

                                <tr class="align-middle">
                                    <th>Partai</th>
                                    <th>No Box</th>
                                    <th>Tipe - ket</th>
                                    <th class="text-end">Pcs Awal</th>
                                    <th class="text-end">Gr Awal</th>
                                    <th class="text-end">Pcs Akhir</th>
                                    <th class="text-end">Gr Akhir</th>
                                    <th class="text-end">Pcs Ok</th>
                                    <th class="text-end">Pcs Pth</th>
                                    <th class="text-end">Pcs Pth Sortir</th>
                                    <th class="text-end">Total Pcs Pth</th>
                                </tr>
                            </thead>
                            @php
                                $ttlPcs = 0;
                                $ttlGr = 0;
                                $ttlPcsSrt = 0;
                                $ttlGrSrt = 0;

                                $ttlPcsPth = 0;
                                $ttlGrPth = 0;
                            @endphp
                            @foreach ($formulir as $d)
                                @php
                                    $ttlPcs += $d->pcs;
                                    $ttlGr += $d->gr;

                                    $ttlPcsSrt += $d->pcs_srt;
                                    $ttlGrSrt += $d->gr_srt;

                                    $pcsPth = DB::selectOne("SELECT sum(a.pcs) as pcs 
                                FROM tb_hancuran as a
                                where a.kategori in('cetak','sortir') and a.no_box = '$d->no_box'
                                ");

                                    $pcs_pth_grade = DB::table('tb_hancuran')
                                        ->where('no_box', $d->no_box)
                                        ->where('kategori', 'grade')
                                        ->first();

                                    $ttlPcsPth += $pcsPth->pcs ?? 0;
                                    $ttlGrPth += $pcs_pth_grade->pcs ?? 0;
                                @endphp
                                <tbody>
                                    <tr>
                                        <td>{{ $d->nm_partai }}</td>
                                        <td>{{ $d->no_box }}</td>
                                        <td>{{ $d->tipe . ' - ' . $d->ket }}</td>
                                        <td class="text-end">{{ $d->pcs_srt }}</td>
                                        <td class="text-end">{{ $d->gr_srt }}</td>
                                        <td class="text-end">{{ $d->pcs }}</td>
                                        <td class="text-end">{{ $d->gr }}</td>
                                        <td class="text-end">
                                            {{ number_format($d->pcs - ($pcsPth->pcs ?? 0) - ($pcs_pth_grade->pcs ?? 0), 0) }}
                                        </td>
                                        <td class="text-end">{{ number_format($pcsPth->pcs ?? 0, 0) }}</td>
                                        <td width="10%" class="text-end">
                                            <input type="text" class="form-control form-control-sm text-end"
                                                name="pcs_pth[]" value="{{ $pcs_pth_grade->pcs ?? 0 }}">
                                            <input type="hidden" name="no_box[]" value="{{ $d->no_box }}">
                                        </td>
                                        <td class="text-end">
                                            {{ number_format(($pcsPth->pcs ?? 0) + ($pcs_pth_grade->pcs ?? 0), 0) }}
                                        </td>

                                    </tr>
                                </tbody>
                            @endforeach
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th class="text-end">{{ number_format($ttlPcsSrt, 0) }}</th>
                                    <th class="text-end">{{ number_format($ttlGrSrt, 0) }}</th>

                                    <th class="text-end">{{ number_format($ttlPcs, 0) }}</th>
                                    <th class="text-end">{{ number_format($ttlGr, 0) }}</th>
                                    <th class="text-end">{{ number_format($ttlPcs - $ttlPcsPth - $ttlGrPth, 0) }}</th>
                                    <th class="text-end">{{ number_format($ttlPcsPth, 0) }}</th>
                                    <th class="text-end">{{ number_format($ttlGrPth, 0) }}</th>
                                    <th class="text-end">{{ number_format($ttlGrPth + $ttlPcsPth, 0) }}</th>

                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </form>

</body>

</html>
