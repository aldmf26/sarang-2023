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
    <br>
    <form action="{{ route('gudangsarang.gethancuran.savePthGrading') }}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between print_hilang">
                <a href="#"></a>
                <button type="submit" class="print print_hilang btn btn-sm btn-primary">
                    <i class="fa-solid fa-save"></i> Save
                </button>
            </div>
            <div>
                <h5 class="fw-bold text-center " style="text-decoration: underline">PO GRADING : {{ $no_invoice }}
                </h5>
                @php
                    $f = $formulir[0];
                @endphp
                <table class="table">
                    <tr>
                        <th width="1">Tanggal</th>
                        <td width="1">:</td>
                        <td>{{ tanggal(date('Y-m-d')) }}</td>
                    </tr>
                    <tr>
                        <th width="150">Nama Partai</th>
                        <td width="1">:</td>
                        <td>{{ $f->nm_partai }}</td>
                    </tr>
                    <tr>
                        <th>Tipe</th>
                        <td width="1">:</td>
                        <td>{{ $f->tipe }} - {{ $f->ket }}</td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <input type="hidden" name="no_invoice" value="{{ $no_invoice }}">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="14%">Pgws</th>
                                <th width="14%">No Box</th>
                                <th width="14%" class="text-end">Pcs</th>
                                <th width="14%" class="text-end">Gr</th>
                                <th width="14%" class="text-end">Pcs Ok</th>
                                <th width="14%" class="text-end">Ttl Pcs Turun Grade</th>
                                <th width="14%" class="text-end">Turun Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlPcsPth = 0;
                                $ttlGrPth = 0;
                            @endphp
                            @foreach ($formulir as $i => $d)
                                @php
                                    $pcsPth = DB::selectOne("SELECT sum(a.pcs) as pcs 
                                FROM tb_hancuran as a
                                where a.kategori in('cetak','sortir','grade') and a.no_box = '$d->no_box'
                                ");
                                    $pcs_pth_grade = DB::table('tb_hancuran')
                                        ->where('no_box', $d->no_box)
                                        ->where('kategori', 'grading')
                                        ->first();

                                    $ttlPcsPth += $pcsPth->pcs ?? 0;
                                    $ttlGrPth += $pcs_pth_grade->pcs ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $d->pgws }}</td>
                                    <td>{{ $d->no_box }}</td>
                                    <td class="text-end">{{ $d->pcs }}</td>
                                    <td class="text-end">{{ $d->gr }}</td>
                                    <td class="text-end">
                                        {{ $d->pcs - ($pcsPth->pcs ?? 0) - ($pcs_pth_grade->pcs ?? 0) }}
                                    </td>
                                    <td class="text-end">{{ $pcsPth->pcs ?? 0 }}</td>
                                    <td class="text-end">
                                        <input type="text" class="form-control form-control-sm text-end"
                                            name="pcs_pth[]" value="{{ $pcs_pth_grade->pcs ?? 0 }}">
                                        <input type="hidden" name="no_box[]" value="{{ $d->no_box }}">
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Add more rows as needed -->
                        </tbody>
                        <tfoot>
                            <tr></tr>
                            <th colspan="2">Total :</th>
                            <th class="text-end">{{ sumCol($formulir, 'pcs') }}</th>
                            <th class="text-end">{{ sumCol($formulir, 'gr') }}</th>
                            <th class="text-end">
                                {{ number_format(sumCol($formulir, 'pcs') - $ttlPcsPth - $ttlGrPth, 0) }}
                            </th>
                            <th class="text-end">{{ number_format($ttlPcsPth, 0) }}</th>
                            <th class="text-end">{{ number_format($ttlGrPth, 0) }}</th>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>
    </form>

</body>

</html>
