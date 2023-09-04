<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$title}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<style>
    .table1 {
        font-family: sans-serif;
        color: #232323;
        border-collapse: collapse;
    }

    .table1,
    th,
    td {
        border: 1px solid #999;
        padding: 1px 20px;
        font-size: 9px;
    }
</style>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <h4 class=" fw-bold mb-4 text-center text-decoration-underline" style="color: #787878">Print BK</h4>

                <br>
                <table class="table1 table-bordered" width="100%">
                    <thead style="background-color: #E9ECEF;">
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">No Lot</th>
                            <th class="dhead">No Box</th>
                            <th class="dhead">Tipe</th>
                            <th class="dhead">Ket</th>
                            <th class="dhead">Warna</th>
                            <th class="dhead">Tgl terima</th>
                            <th class="dhead">Pengawas</th>
                            <th class="dhead">Penerima</th>
                            <th class="dhead">Pcs Awal</th>
                            <th class="dhead">Gr Awal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($no_nota as $no => $n)
                        @php
                        $nota = DB::selectOne("SELECT * FROM bk as a
                        left join ket_bk as b on b.id_ket_bk = a.id_ket
                        left join warna as c on c.id_warna = a.id_warna
                        where a.no_box = $n
                        ")
                        @endphp
                        <tr>
                            <td>{{$no+1}}</td>
                            <td>{{$nota->no_lot}}</td>
                            <td>{{$nota->no_box}}</td>
                            <td>{{$nota->tipe}}</td>
                            <td>{{$nota->ket_bk}}</td>
                            <td>{{$nota->nm_warna}}</td>
                            <td>{{date('d-m-Y',strtotime($nota->tgl))}}</td>
                            <td>{{$nota->pengawas}}</td>
                            <td>{{strtoupper($nota->penerima == '1' ? 'Jenah' : ($nota->penerima == '2' ? 'Nurul' :
                                'Erna'))}}
                            </td>
                            <td>{{$nota->pcs_awal}}</td>
                            <td>{{$nota->gr_awal}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>



            </div>
        </div>
    </div>

</body>

<script>
    window.print()
</script>

</html>