@props([
    'title' => '',
    'pcs' => '',
    'gr' => '',
    'ttl_rp' => '',
])
<style>
    .card-border {
        border: 2px solid #EFEFEF;
    }

    .card-border:hover {
        border: 1px solid #129FB5;
    }
</style>
<div class="card  card-border">
    <div class="card-body">
        <h6>{{ $title }}</h6>
        <div class="row">
            <div class="col-lg-3  mt-2">
                <p>Pcs : {{ number_format($pcs, 0) }}</p>
            </div>
            <div class="col-lg-3  mt-2">
                <p>Gr : {{ number_format($gr, 0) }}</p>
            </div>
            <div class="col-lg-5  mt-2">
                <p>Ttl Rp : {{ number_format($ttl_rp, 0) }}</p>
            </div>
        </div>

    </div>
</div>
