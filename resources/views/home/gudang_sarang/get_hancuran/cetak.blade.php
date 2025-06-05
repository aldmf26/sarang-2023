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
    <form action="{{ route('gudangsarang.gethancuran.savePthCetak') }}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between print_hilang">
                <a href="{{ route('gudangsarang.invoice_sortir', ['kategori' => 'sortir']) }}"
                    class="print_hilang btn btn-sm btn-warning"><i class="fa-solid fa-left-long"></i>
                    Kembali</a>

                <button type="submit" class="print print_hilang btn btn-sm btn-primary float-end"><i
                        class="fa-solid fa-save"></i>
                    Save</button>
            </div>
            @foreach ($halaman as $h)
                <div class="section">
                    <h5 class="fw-bold text-center" style="text-decoration: underline">PO SORTIR : {{ $no_invoice }}
                    </h5>

                    <h6 class="fw-bold">Pengawas : {{ auth()->user()->find($h->id_pemberi)->name }} ~
                        {{ $h->name }} </h6>
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="no_invoice" value="{{ $no_invoice }}">
                            <table class="table table-bordered" style="font-size: 13px; border:1px solid black">
                                <thead>
                                    <tr class="align-middle">
                                        <th width="80">Nama Partai</th>
                                        <th width="70">No Box</th>
                                        <th width="89">Grade</th>
                                        <th width="60" class="text-end">Pcs Awal Ctk</th>
                                        <th width="60" class="text-end">Gr Awal Ctk</th>
                                        <th width="60" class="text-end">Pcs Akhir</th>
                                        <th width="60" class="text-end">Gr Akhir</th>
                                        <th width="60" class="text-end">Pth Cabut</th>
                                        <th width="60" class="text-end">Pth Ctk</th>
                                        <th width="60" class="text-end">Total Pth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $detail = DB::table('formulir_sarang as a')
                                            ->leftJoin('users', 'users.id', '=', 'a.id_penerima')
                                            ->leftJoin('bk as b', function ($join) {
                                                $join
                                                    ->on('b.no_box', '=', 'a.no_box')
                                                    ->where('b.kategori', '=', 'cabut');
                                            })
                                            ->leftJoin('cetak_new as c', 'c.no_box', '=', 'a.no_box')
                                            ->where('a.no_invoice', $no_invoice)
                                            ->where('a.kategori', 'sortir')
                                            ->where('a.id_penerima', $h->id_penerima)
                                            ->selectRaw(
                                                'a.id_formulir,a.sst_aktual,b.nm_partai, b.tipe as tipe, b.ket, a.no_box, a.pcs_awal, a.gr_awal,c.pcs_awal_ctk as pcs_cbt, c.gr_awal_ctk as gr_cbt',
                                            )
                                            ->get()
                                            ->toArray();

                                        $ttlPcs_cbt = 0;
                                        $ttlPcs_ctk = 0;
                                    @endphp
                                    @foreach ($detail as $d)
                                        <tr>

                                            <td>{{ $d->nm_partai }}</td>
                                            <td>{{ $d->no_box }}</td>
                                            <td>{{ $d->tipe . ' - ' . $d->ket }}</td>
                                            <td class="text-end">{{ $d->pcs_cbt }}</td>
                                            <td class="text-end">{{ $d->gr_cbt }}</td>
                                            <td class="text-end">{{ $d->pcs_awal }}</td>
                                            <td class="text-end">{{ $d->gr_awal }}</td>
                                            @php
                                                $pcs_pth_cabut = DB::table('tb_hancuran')
                                                    ->where('no_box', $d->no_box)
                                                    ->where('kategori', 'cetak')
                                                    ->first();
                                                $pcs_pth_ctk = DB::table('tb_hancuran')
                                                    ->where('no_box', $d->no_box)
                                                    ->where('kategori', 'sortir')
                                                    ->first();

                                                $ttlPcs_cbt += $pcs_pth_cabut->pcs ?? 0;
                                                $ttlPcs_ctk += $pcs_pth_ctk->pcs ?? 0;
                                            @endphp
                                            <td class="text-end">{{ number_format($pcs_pth_cabut->pcs ?? 0, 0) }}</td>
                                            <td>

                                                <input type="text" class="form-control form-control-sm text-end"
                                                    name="pcs_pth[]" value="{{ $pcs_pth_ctk->pcs ?? 0 }}">
                                                <input type="hidden" name="no_box[]" value="{{ $d->no_box }}">
                                            </td>
                                            <td class="text-end">
                                                {{ number_format(($pcs_pth_ctk->pcs ?? 0) + ($pcs_pth_cabut->pcs ?? 0), 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th class="text-end">{{ number_format(sumCol($detail, 'pcs_cbt'), 0) }}</th>
                                        <th class="text-end">{{ number_format(sumCol($detail, 'gr_cbt'), 0) }}</th>
                                        <th class="text-end">{{ number_format(sumCol($detail, 'pcs_awal'), 0) }}</th>
                                        <th class="text-end">{{ number_format(sumCol($detail, 'gr_awal'), 0) }}</th>
                                        <td class="text-end">{{ number_format($ttlPcs_cbt, 0) }}</td>
                                        <td class="text-end">{{ number_format($ttlPcs_ctk, 0) }}</td>
                                        <td class="text-end">{{ number_format($ttlPcs_cbt + $ttlPcs_ctk, 0) }} </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </form>

</body>

</html>
