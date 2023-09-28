<div class="row">
    <div class="col-lg-4">
        <label for="">Tipe Kerja</label>
        <select name="tipe" id="" class="form-control mb-2 tipe">
            <option value="cbt" selected>cbt</option>
            <option value="sisa">sisa</option>
        </select>
    </div>
    <div class="col-lg-8 mb-2">
        @php
            $cek = DB::table('cabut')->where([['tgl_terima', date('Y-m-d')], ['id_pengawas', auth()->user()->id]])->first();
        @endphp
        @if (!empty($cek))
        {{-- <button type="button" class="btn btn-sm btn-primary mb-3 btnLanjutkanTambahCabut"><i class="fas fa-arrow-right"></i> Lanjutkan</button> --}}
        
        @endif
        <label for="">Pencarian :</label>
        <td><input autofocus type="text" id="pencarian" class="form-control float-end"></td>
       
        
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered" id="tablealdi">
            <thead>
                <tr>
                    <th class="dhead" width="15">#</th>
                    <th class="dhead">Nama Anak</th>
                    <th class="dhead" width="35">Kelas</th>
                    <th class="dhead">
                        <center>
                            <input style="text-align: center" type="checkbox" class="form-check" id="cekSemua">
                        </center>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($anak as $no => $d)
                    <tr>
                        <td style="display: none">{{ $d->id_anak }}</td>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ strtoupper($d->nama) }}</td>
                        <td>{{ $d->kelas }}</td>
                        <td align="center">
                            <input type="checkbox" class="form-check cek" name="cek[]">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
