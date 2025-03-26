{{-- 
    <a data-bs-toggle="modal" data-bs-target="#tambah" href="#"
                    class="float-end btn btn-sm btn-primary me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
    <a href="{{ route('cabut.export_global', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
    class="float-end btn btn-sm btn-primary me-2">
    <i class="fas fa-file-excel"></i> Export Global
</a>
<a href="{{ route('cabut.export_ibu', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
    class="float-end btn btn-sm btn-primary me-2">
    <i class="fas fa-file-excel"></i> Export Ibu
</a>
<a href="{{ route('cabut.export_sinta', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
    class="float-end btn btn-sm btn-primary me-2">
    <i class="fas fa-file-excel"></i> Export Sinta
</a> --}}
<div class="dropdown float-end ms-2">
    <button class="btn btn-primary  btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-file-excel"></i>
        Export
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="">
        {{-- <a data-bs-toggle="modal" data-bs-target="#tambah" href="#" class="dropdown-item">
            Export Rekap
        </a> --}}

        <a href="{{ route('cabut.export_global', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="dropdown-item ">
            Export Global
        </a>
        {{-- <a href="{{ route('cabut.export_ibu', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="dropdown-item ">
            Export Ibu
        </a>
        <a href="{{ route('cabut.export_sinta', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="dropdown-item ">
            Export Sinta
        </a> --}}
    </div>
</div>
@include('home.cabut.view_bulandibayar')
