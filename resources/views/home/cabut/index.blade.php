<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="{{ route('cabut.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Krywn" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Box</th>
                        {{-- <th>Pengawas</th> --}}
                        <th>Anak</th>
                        <th>Tgl Terima</th>
                        <th class="text-end">Pcs Awal</th>
                        <th class="text-end">Gr Awal</th>
                        <th class="text-end">Pcs Akhir</th>
                        <th class="text-end">Gr Akhir</th>
                        <th class="text-end">Pcs Hcr</th>
                        <th class="text-end">EOT</th>
                        <th class="text-end">Susut</th>
                        {{-- <th class="text-end">Denda</th> --}}
                        <th class="text-end">Ttl Gaji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabut as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $d->no_box }}</td>
                            {{-- <td>{{ ucwords(auth()->user()->name) }}</td> --}}
                            <td>{{ $d->nama }}</td>
                            <td>{{ date('d M y', strtotime($d->tgl_terima)) }}</td>
                            <td align="right">{{ $d->pcs_awal }}</td>
                            <td align="right">{{ $d->gr_awal }}</td>
                            <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                            <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                            <td align="right">{{ $d->pcs_hcr ?? 0 }}</td>
                            <td align="right">{{ $d->eot ?? 0 }}</td>
                            @php
                                $susut = empty($d->gr_akhir) ? 0 : (1 - ($d->gr_flx + $d->gr_akhir) / $d->gr_awal) * 100;
                                
                                $denda = empty($d->gr_akhir) ? 0 : ($susut > 23.4 ? ($susut - 23.4) * 0.03 * $d->rupiah : 0);
                                $denda_hcr = $d->pcs_hcr * 5000;
                                
                                $eot_bonus = empty($d->eot) ? 0 : ($d->eot - $d->gr_awal * 0.02) * 750;
                            @endphp
                            <td align="right">{{ number_format($susut, 0) }}%</td>
                            {{-- <td align="right">{{ number_format($denda,0)}}</td> --}}
                            <td align="right">{{ number_format($d->rupiah - $denda + $eot_bonus, 0) }}</td>
                            <td align="center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                                    class="btn btn-sm btn-primary detail" id_cabut="{{ $d->id_cabut }}"><i
                                        class="fas fa-eye"></i></a>
                                @if ($d->selesai == 'T')
                                    <a class="btn btn-warning btn-sm inputAkhir" href="#"
                                        no_box="{{ $d->no_box }}" id_anak="{{ $d->id_anak }}" href="#"
                                        data-bs-toggle="modal" data-bs-target="#inputAkhir"></i>Akhir</a>

                                    @if (!empty($d->eot))
                                        <a class="btn btn-primary btn-sm selesai" href="#"
                                            id_cabut="{{ $d->id_cabut }}" href="#" data-bs-toggle="modal"
                                            data-bs-target="#selesai"></i>Selesai</a>
                                    @endif
                                @endif



                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </section>

        <form action="{{ route('cabut.input_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut akhir" btnSave="Y" size="modal-lg">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form>

        <form action="{{ route('cabut.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah Anak" btnSave="Y">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label for="">Tambah Anak</label>
                            <div id="load_anak_nopengawas"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-sm btn-primary" type="button" id="add_anak">Edit/Save</button>
                    </div>
                </div>
                <div id="load_anak"></div>
            </x-theme.modal>
        </form>

        <x-theme.modal idModal="detail" title="Detail Cabut" size="modal-lg-max" btnSave="T">
            <div class="row">
                <div class="col-lg-12">
                    <div id="load_detail_cabut"></div>
                </div>
            </div>
        </x-theme.modal>
        <form action="{{ route('cabut.selesai_cabut') }}" method="post">
            @csrf
            <x-theme.modal idModal="selesai" title="Selesai" btnSave="Y" color_header="modal-success">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-center">Apakah anda yakin ingin menyelesaikannya ?</p>
                        <p class="text-center fw-bold">Note : </p>
                        <p class="text-center fw-bold fst-italic">Data yang sudah diselesaikan tidak dapat di edit
                            maupun dihapus
                        </p>
                        <input type="hidden" name="id_cabut" class="cetak">
                    </div>
                </div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(".select3").select2()

                load_anak()
                load_anak_nopengawas()

                function load_anak() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_anak') }}",
                        success: function(r) {
                            $("#load_anak").html(r);
                        }
                    });
                }

                function load_anak_nopengawas() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_anak_nopengawas') }}",
                        success: function(r) {
                            $("#load_anak_nopengawas").html(r)
                            $(".select3-load").select2()

                        }
                    });
                }
                $(document).on('click', '#add_anak', function() {
                    var id_anak = $(".anakNoPengawas").val()
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.add_delete_anak') }}?id_anak=" + id_anak,
                        success: function(r) {
                            alertToast('Berhasil tambah anak')
                            load_anak()
                            load_anak_nopengawas()
                        }
                    });
                })
                $(document).on('click', '#delete_anak', function(e) {

                    var id_anak = $(this).attr('id_anak')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.add_delete_anak') }}",
                        data: {
                            id_anak: id_anak,
                            delete: 1,
                        },
                        success: function(r) {
                            alertToast('Berhasil tambah anak')
                            load_anak()
                            load_anak_nopengawas()
                        }
                    });
                })

                $(document).on('click', '.inputAkhir', function() {
                    var no_box = $(this).attr('no_box')
                    var id_anak = $(this).attr('id_anak')
                    $.ajax({
                        type: "GET",
                        url: "cabut/load_modal_akhir",
                        data: {
                            no_box: no_box,
                            id_anak: id_anak,
                        },
                        success: function(r) {
                            $("#load_modal_akhir").html(r);
                        }
                    });
                })
                $(document).on('click', '.detail', function() {
                    var id_cabut = $(this).attr('id_cabut')
                    $.ajax({
                        type: "GET",
                        url: "cabut/load_detail_cabut",
                        data: {
                            id_cabut: id_cabut,
                        },
                        success: function(r) {
                            $("#load_detail_cabut").html(r);
                        }
                    });
                })
                $(document).on('click', '.selesai', function() {
                    var id_cabut = $(this).attr('id_cabut');

                    $('.cetak').val(id_cabut);
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
