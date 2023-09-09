<div class="row">
    <div class="col-lg-6">
        <table class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Tanggal</th>
                    <th class="dhead">Nama</th>
                    <th class="text-end dhead">Pcs Awal</th>
                    <th class="text-end dhead">Gr Awal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$grade->no_box}}</td>
                    <td>{{date('d M y',strtotime($grade->tgl))}}</td>
                    <td>{{$grade->nama}}</td>
                    <td class="text-end">{{$grade->pcs_awal}}</td>
                    <td class="text-end">{{$grade->gr_awal}}</td>
                </tr>

            </tbody>
        </table>
    </div>
    <div class="col-lg-12">
        <hr>
    </div>
    <div class="col-lg-6">
        <h6>Bentuk</h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="dhead">Grade</th>
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Grade</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_pcs_bentuk = 0;
                $total_gr_bentuk = 0;
                @endphp
                @foreach ($grading_bentuk as $g)
                @php
                $total_pcs_bentuk += $g->pcs;
                $total_gr_bentuk += $g->gram;
                @endphp
                <tr>
                    <td>{{$g->tipe}}</td>
                    <td class="text-end">{{$g->pcs}}</td>
                    <td class="text-end">{{$g->gram}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-6">
        <h6>Turun Grade</h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="dhead">Grade</th>
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Grade</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_pcs_turun = 0;
                $total_gr_turun = 0;
                @endphp
                @foreach ($grading_turun as $g)
                @php
                $total_pcs_turun += $g->pcs;
                $total_gr_turun += $g->gram;
                @endphp
                <tr>
                    <td>{{$g->tipe}}</td>
                    <td class="text-end">{{$g->pcs}}</td>
                    <td class="text-end">{{$g->gram}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-12">
        <hr>
    </div>
    <div class="col-lg-6">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class=""></td>
                    <th class="text-center ">Pcs</th>
                    <th class="text-center ">GR</th>
                </tr>
                <tr>
                    <th class="">Total</th>
                    <th class="text-center">{{$total_pcs_turun + $total_pcs_bentuk}}</th>
                    <th class="text-center">{{$total_gr_turun + $total_gr_bentuk}}</th>
                </tr>
            </tbody>
        </table>
    </div>
</div>