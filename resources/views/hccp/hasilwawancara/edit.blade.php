<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <form action="{{ route('hasilwawancara.update') }}" method="post">
            @csrf
            <section class="row">
                <div class="col-lg-3">
                    <label for="">Nama Karyawan</label>
                    <input type="hidden" class="form-control" name="id" value="{{ $hasil->id }}">
                    <input type="text" class="form-control" name="nama" value="{{ $hasil->nama }}">
                </div>
                <div class="col-lg-3">
                    <label for="">Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tgl_lahir" value="{{ $hasil->tgl_lahir }}">
                </div>
                <div class="col-lg-3">
                    <label for="">Jenis Kelamin</label>
                    <select id="" class="form-control" name="jenis_kelamin">
                        <option value="P" @selected($hasil->jenis_kelamin == 'P')>Perempuan</option>
                        <option value="L" @selected($hasil->jenis_kelamin == 'L')>Laki-Laki</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label for="">Posisi</label>
                    <input type="text" class="form-control" name="posisi" value="{{ $hasil->posisi }}">
                </div>

                <div class="col-lg-9 mt-2">
                    <label for="">Kesimpulan</label>
                    <textarea name="kesimpulan" id="" cols="30" rows="5" class="form-control">
                        {{ $hasil->kesimpulan }}
                    </textarea>
                </div>
                <div class="col-lg-3 mt-2">
                    <label for="">Keputusan</label>
                    <br>
                    <input type="radio" name="keputusan" id="" value="dilanjutkan"
                        {{ $hasil->keputusan == 'dilanjutkan' ? 'checked' : '' }}> Dilanjutkan
                    <input type="radio" name="keputusan" id="" value="ditolak"
                        {{ $hasil->keputusan == 'ditolak' ? 'checked' : '' }}> Ditolak
                </div>

                <div class="col-lg-12 mt-4">
                    <button type="submit" class="btn  btn-primary float-end">Simpan</button>
                    <a href="{{ route('hasilwawancara.index') }}" class="btn  btn-secondary me-2 float-end">Batal</a>
                </div>
            </section>
        </form>
        @section('scripts')
        @endsection
    </x-slot>

</x-theme.app>
