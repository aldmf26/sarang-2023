<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <form action="{{ route('hrga3.store') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-4">
                    <label for="">Nama Karyawan</label>
                    <select name="id_karyawan" class="form-control select2 selectKaryawan" id="">
                        <option value="">- Pilih Karyawan -</option>
                        @foreach ($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label for="">Usia</label>
                    <input readonly type="text" name="usia" class="form-control">
                </div>
                <div class="col-lg-2">
                    <label for="">Jenis Kelamin</label>
                    <input readonly type="text" name="j_kelamin" class="form-control">
                </div>
                <div class="col-lg-4">
                    <label for="">Posisi</label>
                    <input readonly type="text" name="posisi" class="form-control">
                </div>
            </div>

            <table class="mt-4 table table-bordered table-striped">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center dhead">PENILAIAN KARYAWAN</th>
                    </tr>
                    <tr class="text-center">
                        <th class="dhead">Kriteria Penilaian</th>
                        <th class="dhead">Standar Penilaian</th>
                        <th class="dhead">Hasil Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pendidikan</td>
                        <td>
                            <input id="pendidikanStandar" name="penilaian[pendidikan][standar]" type="text"
                                value="N / A" class="form-control">
                        </td>
                        <td>
                            <input name="penilaian[pendidikan][hasil]" type="text" value="N / A"
                                class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Pengalaman</td>
                        <td>
                            <input name="penilaian[pengalaman][standar]" type="text" value="N / A"
                                class="form-control">
                        </td>
                        <td>
                            <input name="penilaian[pengalaman][hasil]" type="text" value="N / A"
                                class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Pelatihan</td>
                        <td>
                            <input name="penilaian[pelatihan][standar]" type="text" value="N / A"
                                class="form-control">
                        </td>
                        <td>
                            <input name="penilaian[pelatihan][hasil]" type="text" value="N / A"
                                class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Keterampilan</td>
                        <td>
                            <input name="penilaian[keterampilan][standar]" type="text" value="N / A"
                                class="form-control">
                        </td>
                        <td>
                            <input name="penilaian[keterampilan][hasil]" type="text" value="N / A"
                                class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Kompetensi Inti</td>
                        <td>
                            <input name="penilaian[kompetensi_inti][standar]" type="text" value="N / A"
                                class="form-control">
                        </td>
                        <td>
                            <input name="penilaian[kompetensi_inti][hasil]" type="text" value="N / A"
                                class="form-control">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
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
                    <div class="form-group d-flex gap-2">
                        <label for="">Keputusan : </label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" value="lulus"
                                    id="tetap" required>
                                <label class="form-check-label" for="tetap">Lulus Masa Percobaan</label>
                            </div>
                            <div class="form-check">
                                <input checked class="form-check-input" type="radio" name="keputusan"
                                    value="tidak lulus" id="kontrak" required>
                                <label class="form-check-label" for="kontrak">Tidak Lulus Masa Percobaan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group d-flex gap-2">
                        <label for="">Periode Masa Percobaan : </label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input checked class="form-check-input" type="radio" name="periode" value="1"
                                    id="1bulan" required>
                                <label class="form-check-label" for="1bulan">1 Bulan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="periode" value="3"
                                    id="3bulan" required>
                                <label class="form-check-label" for="3bulan">3 Bulan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="periode" value="6"
                                    id="6bulan" required>
                                <label class="form-check-label" for="6bulan">6 Bulan</label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <a class="btn btn-md btn-info" href="{{ route('hrga1.index') }}">Cancel</a>
            <button class="btn btn-md float-end btn-primary" type="submit">Simpan</button>
        </form>

        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.select2').select2();

                    $('.selectKaryawan').on('change', function() {
                        var id = $(this).val();
                        $.ajax({
                            type: "GET",
                            url: "{{ route('hrga3.getKaryawan') }}",
                            data: {
                                id: id
                            },
                            dataType: "json",
                            success: function(response) {
                                console.log(response);
                                $('input[name="usia"]').val(response.usia);
                                $('input[name="j_kelamin"]').val(response.j_kelamin);
                                $('input[name="posisi"]').val(response.posisi);
                            }
                        });
                    });

                    $('#pendidikanStandar').on('keyup', function() {
                        var val = $(this).val();
                        if (val == 'SMA') {
                            $('input[name="keputusan"][value="lulus"]').prop('checked', true);
                        }
                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
