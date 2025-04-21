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

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="d-flex justify-content-between print_hilang">
            <a href="{{ route('gudangsarang.gudang_cbt_selesai') }}" class="print_hilang btn btn-sm btn-warning"><i
                    class="fa-solid fa-left-long"></i>
                Kembali</a>
            <a onclick="window.print()" href="#" class="print print_hilang btn btn-sm btn-primary"><i
                    class="fa-solid fa-print"></i>
                Print</a>
        </div>
        <h5 class="fw-bold text-center" style="text-decoration: underline">PO SUSUT</h5>
        <h6 class="fw-bold">Pengawas : {{ $susut[0]->pemberi->name }} ~ {{ $penerima }}</h6>

        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" style="font-size: 13px">
                    <thead>
                        <tr class="align-middle" style="font-size: 10px">
                            <th width="10">#</th>
                            <th width="100">Tanggal</th>
                            <th width="60" class="text-end">Pcs Awal</th>
                            <th width="60" class="text-end">Gr Awal</th>
                            <th width="60" class="text-end">Gr Akhir</th>
                            <th class="text-center dhead">Sst %</th>
                            <th class="text-center bg-warning">Sst Program</th>
                            <th class="text-center dhead">Rambangan 1</th>
                            <th class="text-center dhead">Rambangan 2</th>
                            <th class="text-center dhead">Rambangan 3</th>
                            <th class="text-center dhead">Sapuan Lantai</th>
                            <th class="text-center dhead">Sesetan</th>
                            <th class="text-center dhead">Bulu</th>
                            <th class="text-center dhead">Pasir</th>
                            <th class="text-center dhead">Rontokan Bk</th>
                            <th class="text-center bg-warning">Ttl Aktual</th>
                            <th class="text-center dhead">Selisih</th>
                            <th class="text-center dhead">Sst %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $ttlPcs = 0;
                            $ttlGr = 0;

                            $ttlPcs_cbt = 0;
                            $ttlGr_cbt = 0;

                        @endphp
                        @foreach ($susut as $no => $f)
                            {{-- @php

                                $grCabut = $f->gr_cbt ?? (0 + $f->gr_eo ?? 0);
                                $ttlPcs += $f->pcs_awal;
                                $ttlGr += $f->gr_awal;

                                $ttlPcs_cbt += $f->pcs_cbt;
                                $ttlGr_cbt += $grCabut;
                            @endphp --}}
                            <tr>
                                <td align="right">{{ $no + 1 }}</td>
                                <td align="right">{{ tanggal($f->tgl) }}</td>
                                <td align="right">{{ number_format($f->pcs_awal, 0) }}</td>
                                <td align="right">{{ number_format($f->gr_awal, 0) }}</td>
                                <td align="right">{{ number_format($f->gr_akhir, 0) }}</td>

                                @php
                                    $sst = (1 - $f->gr_akhir / $f->gr_awal) * 100;
                                @endphp
                                <td align="right">{{ number_format($sst, 0) }}%</td>
                                <td align="right">{{ number_format($f->sst_program, 0) }}</td>
                                <td align="right">{{ number_format($f->rambangan_1, 0) }}</td>
                                <td align="right">{{ number_format($f->rambangan_2, 0) }}</td>
                                <td align="right">{{ number_format($f->rambangan_3, 0) }}</td>
                                <td align="right">{{ number_format($f->sapuan_lantai, 0) }}</td>
                                <td align="right">{{ number_format($f->sesetan, 0) }}</td>
                                <td align="right">{{ number_format($f->bulu, 0) }}</td>
                                <td align="right">{{ number_format($f->pasir, 0) }}</td>
                                <td align="right">{{ number_format($f->rontokan_bk, 0) }}</td>

                                @php
                                    $ttlAktual =
                                        $f->rambangan_1 +
                                        $f->rambangan_2 +
                                        $f->rambangan_3 +
                                        $f->sapuan_lantai +
                                        $f->sesetan +
                                        $f->bulu +
                                        $f->pasir +
                                        $f->rontokan_bk;
                                @endphp

                                <td align="right">{{ number_format($ttlAktual, 0) }}</td>

                                @php
                                    $selisih = $f->sst_program - $ttlAktual;

                                    $sstPersenAktual = (1 - $ttlAktual / $f->sst_program) * 100;

                                @endphp

                                <td align="right">{{ number_format($selisih, 0) }}</td>
                                <td align="right">{{ number_format($sstPersenAktual, 0) }} %</td>


                            </tr>
                        @endforeach
                    </tbody>
                    {{-- <tfoot>
                        <tr>
                            <th class="text-center" colspan="5">Total</th>
                            <th class="text-end">{{ number_format($ttlPcs_cbt, 0) }}</th>
                            <th class="text-end">{{ number_format($ttlGr_cbt, 0) }}</th>
                            <th class="text-end">{{ number_format($ttlPcs, 0) }}</th>
                            <th class="text-end">{{ number_format($ttlGr, 0) }}</th>
                            <th class="text-end">{{ number_format($ttlGr_cbt - $ttlGr, 0) }}</th>
                            <th class="text-end">{{ number_format($formulir->sum('sst_aktual'), 0) }}</th>
                            @for ($i = 0; $i < 8; $i++)
                                <th></th>
                            @endfor
                        </tr>
                    </tfoot> --}}
                </table>
            </div>
        </div>

    </div>
    <script>
        window.print()
    </script>
</body>

</html>
