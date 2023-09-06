<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead">No Box</th>
                    <th class="dhead" width="10%">Grade</th>
                    <th class="dhead">Nama Anak</th>
                    <th class="dhead">Pcs Awal</th>
                    <th class="dhead">Gr Awal</th>
                    <th class="dhead text-end" width="150">Pcs Tidak Ctk</th>
                    <th class="dhead text-end" width="150">Gr Tidak CTK</th>
                    <th class="dhead text-end" width="150">Pcs Akhir</th>
                    <th class="dhead text-end" width="150">Gr Akhir</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$cetak->no_box}}</td>
                    <td>{{$cetak->grade}}</td>
                    <td>{{$cetak->nama}}</td>
                    <td>{{$cetak->pcs_awal}}</td>
                    <td>{{$cetak->gr_awal}}</td>
                    <td>
                        <input type="hidden" name="id_cetak" class="form-control" value="{{$cetak->id_cetak}}">
                        <input type="text" name="pcs_tidak_ctk" class="form-control" value="{{$cetak->pcs_tidak_ctk}}">
                    </td>
                    <td><input type="text" name="gr_tidak_ctk" class="form-control" value="{{$cetak->gr_tidak_ctk}}">
                    </td>
                    <td><input type="text" name="pcs_akhir" class="form-control" value="{{$cetak->pcs_akhir}}"></td>
                    <td><input type="text" name="gr_akhir" class="form-control" value="{{$cetak->gr_akhir}}"></td>


                </tr>
            </tbody>
        </table>
    </div>
</div>