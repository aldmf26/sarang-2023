@props([
    'pesan' => '',
])
@if (session()->has('error'))
    <div class="col-lg-12">
        <div class="alert alert-danger">
            <i class="bi bi-file-excel"></i> {{ session()->get('error') }}.
        </div>
    </div>
@endif
@if (session()->has('sukses'))
    <div class="col-lg-12">
        <div class="alert alert-success">
            <i class="bi bi-file-excel"></i> {{ session()->get('sukses') }}.
        </div>
    </div>
@endif
