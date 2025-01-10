<div class="row baris{{ $count }}">

    <div class="col-lg-2 mt-2">

        <input type="text" class="form-control" name="kriteria[]">
    </div>
    <div class="col-lg-2 mt-2">

        <input type="text" class="form-control" name="metode[]">
    </div>
    <div class="col-lg-2 mt-2">

        <select name="hasil_pemeriksaan[]" class="form-control" id="">
            <option value="Ok">Ok</option>
            <option value="Tidak Ok">Tidak Ok</option>
        </select>
    </div>
    <div class="col-lg-2 mt-2">

        <input type="text" class="form-control" name="status[]">
    </div>

    <div class="col-lg-2 mt-2">

        <input type="text" class="form-control" name="ket[]">
    </div>
    <div class="col-lg-1 mt-2">

        <button type="button" onclick="" class="btn btn-sm btn-danger delete_baris" count="{{ $count }}"><i
                class="fa fa-minus"></i></button>
    </div>

</div>
