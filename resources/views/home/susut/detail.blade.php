<p><b>{{ $title }}</b> Tgl : {{ tanggal($lastSusut->tgl ?? date('Y-m-d')) }}</p>
<h5>Pengawas : {{ $nama }}</h5>
<h5>Sst Program : {{ number_format($datas['sst_program'],0) }}</h5>
    <input type="hidden" name="id_pengawas" value="{{ $id_pengawas }}">
    <input type="hidden" name="sst_program" value="{{ $datas['sst_program'] }}">
    <input type="hidden" name="pcs_awal" value="{{ $datas['pcs_awal'] }}">
    <input type="hidden" name="gr_awal" value="{{ $datas['gr_awal'] }}">
    <input type="hidden" name="gr_akhir" value="{{ $datas['gr_akhir'] }}">
    <input type="text" name="divisi" value="{{ $datas['divisi'] }}">
    <table class="table table-sm table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Detail Susut</th>
                <th>Input</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailSusut as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d }}</td>
                    <td><input type="number" class="form-control form-control-sm" name="detailSusut[{{ $i }}]"
                            value="{{ $defaultValues[$i] }}"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
