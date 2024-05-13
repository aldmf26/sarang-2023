<x-theme.app title="{{ $title }}" table="Y" sizeCard="6">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>

        <x-theme.button modal="Y" idModal="tambah" href="#" icon="fa-calendar-alt" addClass="float-end"
            teks="Detail" />
        {{-- <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div> --}}
        {{-- @include('home.cetak.nav') --}}


    </x-slot>

    <x-slot name="cardBody">
        <style>
            .badge {
                cursor: pointer;
            }
        </style>
        <form id="formAbsen" method="POST">
            @csrf
            <section class="row">
                <div class="col-lg-4 mb-2">
                    <div class="form-group">
                        <label for="">Pencarian : </label>
                        <input autofocus type="text" id="pencarian" class="form-control float-end">
                    </div>

                </div>
                <div class="col-lg-5 mb-2">
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <input type="date" value="{{ $tgl }}" name="tgl" id="tglChange"
                            class="form-control">
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <label for="">Aksi</label><br>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </div>
                <div class="col-lg-12">

                    <table style="border:1px solid #97a1c3" class="table table-bordered" id="tablealdi"
                        x-data="{
                            openRows: [],
                        }">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas </th>
                                <th class="dhead">Nama Anak</th>
                                <th class="dhead">Kelas</th>
                                <th class="dhead text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($pengawas as $i => $d)
                                <tr>
                                    <th><a href="#" class="detailAbsen"
                                            id_pengawas="{{ $d->id }}">{{ $d->name }}</a>
                                        <span class="badge bg-primary float-end"
                                            @click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">Buka
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th>
                                        <center>
                                            <input data-category="{{ $i }}" style="text-align: center"
                                                type="checkbox" class="form-check toggle-category" id="cekSemuaTutup">
                                        </center>
                                    </th>
                                </tr>
                                @php
                                    $query = DB::table('tb_anak as a')
                                        ->join('users as b', 'a.id_pengawas', 'b.id')
                                        ->where('a.id_pengawas', $d->id_pengawas)
                                        ->get();
                                @endphp
                                @foreach ($query as $x)
                        <tbody x-show="openRows.includes({{ $i }})">

                            @php
                                $absenHariIni = DB::table('absen')
                                    ->where([['id_anak', $x->id_anak], ['tgl', $tgl]])
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $x->name }}</td>
                                <td>{{ $x->nama }}</td>
                                <td>{{ $x->id_kelas }}</td>
                                <td align="center">
                                    <input {{ !empty($absenHariIni) ? 'checked' : '' }} type="checkbox"
                                        id_anak="{{ $x->id_anak }}" id_pengawas="{{ $x->id_pengawas }}"
                                        data-category="{{ $i }}" class="form-check cekTutup"
                                        name="cekTutup[]">
                                </td>
                            </tr>
                        </tbody>
                        @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </section>
        </form>

        <x-theme.modal idModal="detailAbsen" btnSave="T" title="Detail Absen Anak">

            <div id="loadDetailAbsen"></div>
        </x-theme.modal>
        <form action="{{ route('absen.detailSum') }}" method="get">
            <x-theme.modal idModal="tambah"  title="Detail Absen">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Dari</label>
                            <input type="date" name="tgl1" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Sampai</label>
                            <input type="date" name="tgl2" class="form-control">

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Pengawas</label>
                        <select name="id_pengawas" class="form-control select2" id="">
                            <option value="all">- ALL -</option>
                            @foreach ($pengawas as $d)
                                <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </x-theme.modal>
        </form>
        {{-- <form action="{{ route('absen.detailSum') }}" method="get">
            <x-theme.modal idModal="tambah"  title="Detail Absen">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Bulan</label>
                            <select name="bulan" class="form-control select2" id="">
                                <option value="">- Pilih Bulan -</option>
                                
                                @foreach ($bulan as $b)
                                    <option {{ (int) date('m') == $b->bulan ? 'selected' : '' }} value="{{ $b->id_bulan }}">{{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Tahun</label>
                            <select name="tahun" class="form-control select2" id="">
                                <option value="">- Pilih Tahun -</option>
                                <option value="2022">2022</option>
                                <option value="2023" selected>2023</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Pengawas</label>
                        <select name="id_pengawas" class="form-control select2" id="">
                            <option value="all">- ALL -</option>
                            @foreach ($pengawas as $d)
                                <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </x-theme.modal>
        </form> --}}

        <x-theme.modal idModal="loading" btnSave="T" size="modal-lg" disabled="true" title="Tunggu loading">
            mohon tunggu loading...
            <br>
            <div class="row justify-content-center">
                <img src="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20width='38'%20height='38'%20stroke='%235d79d3'%20viewBox='0%200%2038%2038'%3e%3cg%20fill='none'%20fill-rule='evenodd'%3e%3cg%20stroke-width='2'%20transform='translate(1%201)'%3e%3ccircle%20cx='18'%20cy='18'%20r='18'%20stroke-opacity='.5'/%3e%3cpath%20d='M36%2018c0-9.94-8.06-18-18-18'%3e%3canimateTransform%20attributeName='transform'%20dur='1s'%20from='0%2018%2018'%20repeatCount='indefinite'%20to='360%2018%2018'%20type='rotate'/%3e%3c/path%3e%3c/g%3e%3c/g%3e%3c/svg%3e"
                    class="me-4" style="width: 100px" alt="audio">
            </div>
        </x-theme.modal>
        @section('scripts')
            <script>
                pencarian('pencarian', 'tablealdi')
                $(document).ready(function() {
                    $(document).on('change', '#tglChange', function() {
                        $('#loading').modal('show')
                        var nilai = $(this).val()
                        document.location.href = "?tgl=" + nilai
                    })
                    $(".toggle-category").on("change", function() {
                        var categoryIndex = $(this).data("category");
                        // Cari checkbox subkategori yang sesuai dengan kategori ini
                        $(".cekTutup[data-category='" + categoryIndex + "']").prop("checked", this.checked);
                    });

                    $(document).on('submit', '#formAbsen', function(e) {
                        e.preventDefault();

                        var idAnakWadah = [];
                        var idPengawasWadah = [];
                        var tgl = $("input[name='tgl']").val()
                        // Loop melalui checkbox cekTutup yang dicentang
                        $(".cekTutup:checked").each(function() {
                            var idAnak = $(this).attr("id_anak");
                            idAnakWadah.push(idAnak);

                            var idPengawas = $(this).attr("id_pengawas");
                            idPengawasWadah.push(idPengawas);

                        });


                        $.ajax({
                            type: "GET",
                            url: "{{ route('absen.create') }}",
                            data: {
                                id_anak: idAnakWadah,
                                id_pengawas: idPengawasWadah,
                                tgl: tgl
                            },
                            success: function(r) {
                                window.location.reload()
                            }
                        });
                    })

                    $(document).on('click', '.detailAbsen', function(e) {
                        e.preventDefault()
                        var id_pengawas = $(this).attr('id_pengawas')
                        $.ajax({
                            type: "GET",
                            url: "{{ route('absen.detailAbsen') }}",
                            data: {
                                id_pengawas: id_pengawas
                            },
                            success: function(r) {
                                $('#detailAbsen').modal('show')
                                $("#loadDetailAbsen").html(r);
                                $("#tableDetailAbsen").dataTable()
                            }
                        });
                    })

                    $(document).on('submit', '#viewDetailAbsen', function(e) {
                        e.preventDefault()
                        var id_pengawas = $("input[name='id_pengawas']").val()
                        var bulan = $("select[name='bulan']").val()
                        var tahun = $("select[name='tahun']").val()

                        $.ajax({
                            type: "GET",
                            url: "{{ route('absen.detailAbsen') }}",
                            data: {
                                id_pengawas: id_pengawas,
                                bulan: bulan,
                                tahun: tahun,
                            },
                            success: function(r) {
                                $('#detailAbsen').modal('show')
                                $("#loadDetailAbsen").html(r);
                                $("#tableDetailAbsen").dataTable()
                            }
                        });
                    })
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
