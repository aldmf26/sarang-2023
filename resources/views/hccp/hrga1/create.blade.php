<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <form action="{{ route('hrga1.store') }}" method="post">
            @csrf
            <div class="row ">
                
                <div class="col-lg-12">
                    <div class="form-group d-flex gap-2">
                        <label for="">Status Posisi : </label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_posisi" value="Tetap"
                                    id="tetap" required>
                                <label class="form-check-label" for="tetap">Karyawan Tetap</label>
                            </div>
                            <div class="form-check">
                                <input checked class="form-check-input" type="radio" name="status_posisi"
                                    value="Kontrak" id="kontrak" required>
                                <label class="form-check-label" for="kontrak">Karyawan Kontrak</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Jabatan</label>
                        <input placeholder="cabut bulu" type="text" name="jabatan" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Jumlah</label>
                        <input placeholder="20 orang" type="number" name="jumlah" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Alasan Penambahan</label>
                        <input placeholder="adanya penambahan kapasitas aktivitas cabut bulu" type="text"
                            name="alasan_penambahan" class="form-control" required>
                    </div>
                </div>

            </div>

            <span style="text-decoration: underline;"><b>Kualifikasi</b></span>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Umur</label>
                        <input placeholder="min 18" type="text" name="umur" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Jenis Kelamin</label>
                        <input placeholder="perempuan" type="text" name="j_kelamin" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Pendidikan</label>
                        <input placeholder="open seluruh pengalaman pendidikan" type="text" name="pendidikan"
                            class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Pengalaman</label>
                        <input placeholder="open / non pengalaman" type="text" name="pengalaman"
                            class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Pelatihan</label>
                        <input placeholder="N / A" type="text" name="pelatihan" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Mental / Sikap</label>
                        <input placeholder="Teliti" type="text" name="mental" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <label for="">Uraian Kerja</label>
                        <input placeholder="Aktivitas cabut bulu per tray / nampan dengan kapasitas 5-10 gram"
                            type="text" name="uraian_kerja" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group ">
                        <label for="">Tanggal Dibutuhkan</label>
                        <input type="date" required name="tgl_dibutuhkan" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group ">
                        <label for="">Diajukan Oleh</label>
                        <input type="text" placeholder="Rizwina" required name="diajukan_oleh"
                            class="form-control">
                    </div>
                </div>
            </div>
            <a class="btn btn-md btn-info" href="{{ route('hrga1.index') }}">Cancel</a>
            <button class="btn btn-md float-end btn-primary" type="submit">Simpan</button>
        </form>

    </x-slot>

</x-theme.app>
