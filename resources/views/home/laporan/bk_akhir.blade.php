<div class="row">
    <div class="col-lg-12 mb-4">
        <h5>Partai : {{ $partai }}</h5>
    </div>
    <div class="col-lg-6">
        <label for="">Pcs Akhir</label>
        <input type="hidden" name="partai" value="{{ $partai }}">
        <input type="text" class="form-control" name="pcs_akhir" value="{{ $bk_akhir->pcs ?? 0 }}">
    </div>
    <div class="col-lg-6">
        <label for="">Pcs Akhir</label>
        <input type="text" class="form-control" name="gr_akhir" value="{{ $bk_akhir->gr ?? 0 }}">
    </div>
</div>
