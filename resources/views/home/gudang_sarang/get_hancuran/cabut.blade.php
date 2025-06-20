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
    <form action="{{ route('gudangsarang.gethancuran.savePthCabut') }}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between print_hilang">
                <a href="{{ route('gudangsarang.invoice') }}" class="print_hilang btn btn-sm btn-warning"><i
                        class="fa-solid fa-left-long"></i>
                    Kembali</a>
                <button type="submit" class="print print_hilang btn btn-sm btn-primary"><i
                        class="fa-solid fa-save"></i>
                    Save</button>
            </div>
            <h5 class="fw-bold text-center" style="text-decoration: underline">Po Cetak : {{ $no_invoice }}</h5>

            <h6 class="fw-bold">Pengawas : {{ $ket_formulir->name }} ~ {{ $ket_formulir->penerima }} | Tanggal :
                {{ tanggal($ket_formulir->tanggal) }}</h6>

            <div class="row">
                <div class="col-lg-12">
                    <input type="hidden" name="no_invoice" value="{{ $no_invoice }}">
                    <table class="table table-bordered" style="font-size: 13px">
                        <thead>
                            <tr class="align-middle" style="font-size: 10px">
                                <th width="10">#</th>

                                <th width="70">Nama Partai</th>
                                <th width="70">No Box</th>
                                <th width="70">Grade</th>
                                <th width="60" class="text-end">Pcs Awal</th>
                                <th width="60" class="text-end">Gr Awal</th>
                                <th width="60" class="text-end">Pcs Akhir</th>
                                <th width="60" class="text-end">Gr Akhir</th>
                                <th width="60" class="text-end">Pcs Pth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlPcs = 0;
                                $ttlGr = 0;

                                $ttlPcs_cbt = 0;
                                $ttlGr_cbt = 0;
                                $ttlPcs_pth = 0;

                            @endphp
                            @foreach ($formulir as $no => $f)
                                @php

                                    $grCabut = $f->gr_cbt ?? (0 + $f->gr_eo ?? 0);
                                    $ttlPcs += $f->pcs_awal;
                                    $ttlGr += $f->gr_awal;

                                    $ttlPcs_cbt += $f->pcs_cbt;
                                    $ttlGr_cbt += $grCabut;

                                    $pcs_hcr = DB::table('tb_hancuran')
                                        ->where('no_box', $f->no_box)
                                        ->where('kategori', 'cabut')
                                        ->first();
                                    $ttlPcs_pth += $pcs_hcr->pcs ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $no + 1 }}</td>

                                    <td>{{ $f->nm_partai }}</td>
                                    <td>{{ $f->no_box }}</td>
                                    <td>{{ $f->tipe }}-{{ $f->ket }}</td>
                                    <td class="text-end">{{ $f->pcs_cbt ?? 0 }}</td>
                                    <td class="text-end">{{ $grCabut }}</td>
                                    <td class="text-end">{{ $f->pcs_awal }}</td>
                                    <td class="text-end">{{ $f->gr_awal }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-end"
                                            name="pcs_pth[]"
                                            value="{{ empty($pcs_hcr->pcs) ? $f->ket_hcr ?? 0 : $pcs_hcr->pcs }}">
                                        <input type="hidden" name="no_box[]" value="{{ $f->no_box }}">
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center" colspan="4">Total</th>
                                <th class="text-end">{{ number_format($ttlPcs_cbt, 0) }}</th>
                                <th class="text-end">{{ number_format($ttlGr_cbt, 0) }}</th>
                                <th class="text-end">{{ number_format($ttlPcs, 0) }}</th>
                                <th class="text-end">{{ number_format($ttlGr, 0) }}</th>
                                <th class="text-end">{{ number_format($ttlPcs_pth, 0) }}</th>


                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </form>


</body>

</html>
