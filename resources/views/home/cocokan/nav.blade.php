@php
    $rot = request()->route()->getName();
@endphp
<div class="col-lg-8">
    <ul class="nav nav-pills float-start">
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
            <a class="nav-link  {{ $rot == 'cocokan.list_pengiriman' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.list_pengiriman') }}">List Pengiriman</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link  {{ $rot == 'cocokan.opname' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.opname') }}">Opname</a>
        </li> --}}
    </ul>
</div>
<div class="col-lg-4">
    <a href="{{ route('opnamenew.export') }}" class="btn btn-primary btn-sm float-end"><i
            class="fas fa-file-excel"></i>Export</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#cost_opr_input"
        class="btn btn-primary btn-sm float-end me-2">Cost operasional</a>
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
                    <button class="float-end btn btn-primary button-save-modal-loading" type="button" disabled hidden>
                        <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
                        Loading...
                    </button>


                </div>

            </div>
        </div>
    </div>
</form>
