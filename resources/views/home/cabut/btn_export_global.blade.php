<a href="{{ route('cabut.export_global', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
    class="float-end btn btn-sm btn-primary me-2">
    <i class="fas fa-file-excel"></i> Export Global
</a>
<a href="{{ route('cabut.export_ibu', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
    class="float-end btn btn-sm btn-primary me-2">
    <i class="fas fa-file-excel"></i> Export Ibu
</a>
@include('home.cabut.view_bulandibayar')
