<x-theme.app title="{{ $title }}" table="Y" sizeCard="6">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">

            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            {{-- @include('home.cetak.nav') --}}

        </div>

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
                        <input type="date" value="{{ date('Y-m-d') }}" name="tgl" class="form-control">
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
                                        <center
                                            @click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">
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
                                    ->where([['id_anak', $x->id_anak], ['tgl', date('Y-m-d')]])
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
        @section('scripts')
            <script>
                pencarian('pencarian', 'tablealdi')
                $(document).ready(function() {

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
