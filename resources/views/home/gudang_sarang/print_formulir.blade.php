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
        <a href="{{ route('gudangsarang.gudang_cbt_selesai') }}" class="print_hilang btn btn-sm btn-warning"><i
                class="fa-solid fa-left-long"></i>
            Kembali</a>
        <a href="#" class="print print_hilang btn btn-sm btn-primary"><i class="fa-solid fa-print"></i> Print</a>
        <h5 class="fw-bold text-center" style="text-decoration: underline">FORMULIR SETOR BARANG KE CETAK</h5>
        <h6 class="fw-bold">Pengawas : {{ $ket_formulir->name }} ~ {{ $ket_formulir->penerima }}</h6>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" style="font-size: 13px">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>tanggal</th>
                            <th>no box</th>
                            <th class="text-end">pcs awal</th>
                            <th class="text-end">gr awal</th>
                            <th class="text-end">pcs tidak ctk</th>
                            <th class="text-end">gr tidak ctk</th>
                            <th>nama anak</th>
                            <th class="text-end">pcs akhir</th>
                            <th class="text-end">gr akhir</th>
                            <th class="text-end">pcs hcr</th>
                            <th class="text-end">susut%</th>
                            <th class="text-end">total gaji</th>
                            <th>capai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formulir as $no => $f)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ date('d-m-Y', strtotime($f->tanggal)) }}</td>
                                <td>{{ $f->no_box }}</td>
                                <td class="text-end">{{ $f->pcs_awal }}</td>
                                <td class="text-end">{{ $f->gr_awal }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.print').click(function(e) {
            e.preventDefault();
            window.print()

        });
    </script>
    {{-- <script>
        window.print();
    </script> --}}
</body>

</html>
