@php
    $rot = request()->route()->getName();
@endphp
<div class="d-flex justify-content-between">
    <div>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.index' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.index') }}">Cabut</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.cetak' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.cetak') }}">Cetak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.sortir' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.sortir') }}">Sortir</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.grading' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.grading') }}">Grading</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.pengiriman' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.pengiriman') }}">Pengiriman</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.balancesheet' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.balancesheet') }}">Balance Sheet</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.costPartai' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.costPartai') }}">Cost per partai</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.opname' || $rot == 'cocokan.opname/cetak' || $rot == 'cocokan.opname/sortir' ? 'active' : '' }}"
                    aria-current="page" href="{{ route('cocokan.opname') }}">Opname</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cost_global.index' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cost_global.index') }}">Cost Global</a>
            </li>
        </ul>
    </div>
    <div class="d-none">
        <a href="{{ route('opnamenew.
        ') }}" class="btn btn-primary btn-sm "><i
                class="fas fa-file-excel"></i>Export Opname</a>
        <a href="{{ route('cocokan.export') }}" class="btn btn-primary btn-sm "><i class="fas fa-file-excel"></i>Export
            Detail</a>
        <a href="{{ route('exportcost.export') }}" class="btn btn-primary btn-sm"><i
                class="fas fa-file-excel"></i>Export Rp + Cost Kerja</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#cost_opr_input" class="btn btn-primary btn-sm">Isi
            Cost operasional</a>
        @include('home.cocokan.btn_export_cocokan')
    </div>

    <div class="btn-group dropdown me-1 mb-1" bis_skin_checked="1">
        <button data-bs-toggle="modal" data-bs-target="#cost_opr_input" type="button" class="btn btn-primary">Isi
            Cost operasional</button>
        <button data-bs-toggle="modal" data-bs-target="#tutup" type="button" class="btn btn-primary">Tutup
            balansheet</button>

        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false" data-reference="parent">
            <span class="sr-only">Export</span>
        </button>
        <div class="dropdown-menu" bis_skin_checked="1" style="">
            <h6 class="dropdown-header">Export</h6>
            <a href="{{ route('opnamenew.export') }}" class="dropdown-item">Opname</a>
            <a href="{{ route('cocokan.export') }}" class="dropdown-item">Detail</a>
            <a href="{{ route('exportcost.export') }}" class="dropdown-item">Rp + Cost Kerja</a>
            <a href="{{ route('cocokan.exportCabut') }}" class="dropdown-item">Rp All</a>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>

<form action="{{ route('summary.saveoprasional') }}" method="post">
    @csrf

    <div class="modal fade" id="cost_opr_input" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Operasional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cost_opr"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>

                    <button type="submit" class="float-end btn btn-primary button-save-modal">Save</button>
                    <button class="float-end btn btn-primary button-save-modal-loading" type="button" disabled
                        hidden>
                        <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="{{ route('cocokan.tutup') }}" method="post">
    @csrf

    <div class="modal fade" id="tutup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tutup Balansheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Balansheet akan di tutup {{ tanggal(date('Y-m-d')) }}</h5>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Bulan</label>
                                <input type="text" readonly name="bulan_ditutup" value="{{ date('M') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Tahun</label>
                                <input type="text" readonly name="tahun_ditutup" value="{{ date('Y') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>

                    <button type="submit" class="float-end btn btn-primary button-save-modal">Save</button>
                    <button class="float-end btn btn-primary button-save-modal-loading" type="button" disabled
                        hidden>
                        <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
