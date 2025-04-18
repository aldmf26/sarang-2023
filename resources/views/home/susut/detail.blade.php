@php
    $totalSusutAktual = DB::table('tb_susut')
        ->where([['id_pemberi', $id_pengawas], ['divisi', 'cabut']])
        ->sum('ttl_aktual');

@endphp
<p><b>{{ $title }}</b> Tgl : <input type="date" name="tgl" class="form-control form-control-sm" value="{{ $lastSusut->tgl ?? date('Y-m-d') }}"></p>
<h5>Pengawas : {{ $nama }}</h5>
<h5>Sst Program : {{ number_format($datas['sst_program'] - $totalSusutAktual, 0) }}</h5>
<input type="hidden" name="id_pengawas" value="{{ $id_pengawas }}">
<input type="hidden" name="sst_program" value="{{ $datas['sst_program'] }}">
<input type="hidden" name="pcs_awal" value="{{ $datas['pcs_awal'] }}">
<input type="hidden" name="gr_awal" value="{{ $datas['gr_awal'] }}">
<input type="hidden" name="gr_akhir" value="{{ $datas['gr_akhir'] }}">
<input type="hidden" name="divisi" value="{{ $datas['divisi'] }}">
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
