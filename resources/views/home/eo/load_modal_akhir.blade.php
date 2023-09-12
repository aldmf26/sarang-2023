<div class="row">
    <input type="hidden" name="id_eo" value="{{ $detail->id_eo }}">
    <input type="hidden" name="id_anak" value="{{ $detail->id_anak }}">
    <input type="hidden" name="no_box" value="{{ $detail->no_box }}">
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead"  width="100">Nama Anak</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Kelas</th>
                    <th class="dhead text-end">Gr EO Awal</th>
                    <th class="dhead">Tgl Serah</th>
                    <th class="dhead text-end">Gr EO Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fs-bold">{{ strtoupper($detail->nama) }}</td>
                
                    <td>
                        <input type="text" readonly value="{{ $detail->no_box }}" class="form-control">
                    </td>
                    @php
                        $kelas = DB::table('tb_kelas_eo')->where('id_kelas', $detail->id_kelas)->first()->kelas;
                    @endphp
                    <td>
                        <input type="text" readonly value="{{ $kelas }}" class="form-control">
                        <input name="id_kelas" type="hidden" value="{{ $detail->id_kelas }}" class="form-control">
                    </td>
                    <td>
                        <input readonly value="{{ $detail->gr_eo_awal }}" type="text" class="form-control text-end">
                    </td>
                    <td><input name="tgl_serah" type="date" value="{{ date('Y-m-d') }}" class="form-control"></td>
                    <td>
                        <input name="gr_eo_akhir" value="{{ $detail->gr_eo_akhir }}" type="text" class="form-control text-end">
                    </td>
                    
               
                </tr>
            </tbody>
        </table>
    </div>
</div>