<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="#" icon="fa-plus" modal="Y" idModal="tambah2" addClass="float-end" teks="Target" />
        <a href="{{ route('cetak.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm icon icon-left btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>
        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end"
            teks="kry baru" />
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-bordered" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Box</th>
                        <th>Grade</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th></th>
                        <th class="text-end">Awal</th>
                        <th class="text-end">Tidak Cetak</th>
                        <th class="text-end">Akhir</th>
                        <th class="text-end">Susut</th>
                        <th class="text-end">Denda</th>
                        <th class="text-end">Ttl Rp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cetak as $no => $c)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $c->no_box }}</td>
                            <td>VL</td>
                            <td>{{ $c->nama }}</td>
                            <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                            <td>
                                pcs: <br>
                                gr :
                            </td>
                            <td align="right">
                                {{ $c->pcs_awal }} <br> {{ $c->gr_awal }}
                            </td>
                            <td align="right">
                                {{ $c->pcs_tidak_ctk }} <br> {{ $c->gr_tidak_ctk }}
                            </td>
                            <td align="right">
                                {{ $c->pcs_akhir }} <br> {{ $c->gr_akhir }}
                            </td>
                            @php
                                $susut = empty($c->gr_akhir) ? '0' : (1 - $c->gr_akhir / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                                $denda = round($susut, 0) * 50000;
                                
                            @endphp
                            <td align="right">{{ number_format($susut, 1) }} %</td>
                            <td align="right">Rp.{{ number_format($denda, 0) }}</td>
                            <td align="right">Rp. {{ number_format($c->rp_pcs * $c->pcs_awal - $denda, 0) }}</td>
                            <td>
                                @if ($c->selesai == 'T')
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#akhir"
                                        class="btn btn-sm btn-warning akhir" id_cetak="{{ $c->id_cetak }}">Akhir</a>

                                    <a href="#" data-bs-toggle="modal" data-bs-target="#selesai"
                                        class="btn btn-sm btn-primary selesai"
                                        id_cetak="{{ $c->id_cetak }}">Selesai</a>
                                @endif

                            </td>
                        </tr>
                    @endforeach


                </tbody>

            </table>
        </section>

        <form action="{{ route('pengawas.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah Anak" btnSave="Y">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label for="">Tambah Anak</label>
                            <select class="select3" name="" multiple id="">
                                @foreach ($anakNoPengawas as $d)
                                    <option value="{{ $d->id_anak }}">{{ ucwords($d->nama) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-sm btn-primary" type="button">Edit/Save</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <tr>
                                <th width="180">Nama</th>
                                <th width="80">Kelas</th>
                                <th>Tgl Masuk</th>
                                <th>Aksi</th>
                            </tr>
                            @foreach ($anak as $d)
                                <tr>
                                    <td>{{ ucwords($d->nama) }}</td>
                                    <td><input type="text" value="{{ $d->kelas }}" class="form-control"></td>
                                    <td><input type="date" class="form-control"></td>
                                    <td><button class="btn btn-sm btn-danger"><i
                                                class="fas fa-window-close"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <form action="{{ route('cetak.add_target') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah2" size="modal-lg-max" title="tambah Target" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead" width="10%">Grade</th>
                                    <th class="dhead">Nama Anak</th>
                                    <th class="dhead">Tgl Terima</th>
                                    <th class="dhead">Target</th>
                                    <th class="dhead text-end" width="110">Pcs Awal</th>
                                    <th class="dhead text-end" width="110">Gr Awal</th>
                                    <th class="dhead text-end" width="130">Ttl Rp</th>
                                    <th class="dhead">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="no_box[]" id="" class="select2-tambah2 pilihBox"
                                            count="1">
                                            <option value="">Pilih Box</option>
                                            @foreach ($cabut as $c)
                                                <option value="{{ $c->no_box }}">{{ $c->no_box }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="grade[]">
                                    </td>
                                    <td>
                                        <select name="id_anak[]" id="" class="select2-tambah2 pilihAnak"
                                            count="1">
                                            <option value="">Pilih Anak</option>
                                            @foreach ($anak as $d)
                                                <option data-kelas="{{ $d->kelas }}"
                                                    value="{{ $d->id_anak }}">
                                                    ({{ $d->kelas }})
                                                    {{ ucwords($d->nama) }}</option>
                                            @endforeach
                                        </select>
                                        {{-- <input type="hidden" class="setHargaSatuan1"> --}}
                                    </td>
                                    <td><input type="date" class="form-control" name="tgl[]"></td>
                                    <td>
                                        <select name="target[]" id="" class="select2-tambah2 pilihTarget"
                                            count="1">
                                            <option value="">Pilih target</option>
                                            <option value="1">TARGET</option>
                                            <option value="2">CU</option>
                                            <option value="3">LN</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="rp_pcs[]" class="form-control rp_target">
                                        <input type="text" name="pcs_awal[]" class="form-control pcs_awal">
                                    </td>
                                    <td><input type="text" name="gr_awal[]" class="form-control"></td>
                                    <td><input type="text" class="form-control total_rp text-end" readonly></td>
                                    <td></td>

                                </tr>
                            </tbody>
                            <tbody id="tbh_baris">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9">
                                        <button type="button" class="btn btn-block btn-lg tbh_baris"
                                            style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                                            <i class="fas fa-plus"></i> Tambah Baris Baru
                                        </button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </x-theme.modal>
        </form>
        <form action="{{ route('cetak.add_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="akhir" size="modal-lg-max" title="Akhir Target" btnSave="Y">
                <div id="akhir_detail"></div>
            </x-theme.modal>
        </form>
        <form action="{{ route('cetak.selesai') }}" method="post">
            @csrf
            <x-theme.modal idModal="selesai" title="Selesai" btnSave="Y" color_header="modal-success">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-center">Apakah anda yakin ingin menyelesaikannya ?</p>
                        <p class="text-center fw-bold">Note : </p>
                        <p class="text-center fw-bold fst-italic">Data yang sudah diselesaikan tidak dapat di edit
                            maupun dihapus
                        </p>
                        <input type="hidden" name="id_cetak" class="cetak">
                    </div>
                </div>
            </x-theme.modal>
        </form>


        @section('scripts')
            <script>
                $(".select3").select2()

                plusRow(1, 'tbh_baris', "cetak/tbh_baris")
                $(document).on('change', '.pilihTarget', function() {
                    var target = $(this).val()
                    if (target == '1') {
                        $('.rp_target').val(900)
                    } else {
                        $('.rp_target').val(600)
                    }
                });
                $(document).on('keyup', '.pcs_awal', function() {

                    var target = $('.rp_target').val();
                    var pcs = $(this).val();

                    var total = parseFloat(target) * parseFloat(pcs);

                    $('.total_rp').val(total);

                });
                $(document).on('click', '.akhir', function() {
                    var id_cetak = $(this).attr('id_cetak');
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.akhir') }}",
                        data: {
                            id_cetak: id_cetak,
                        },
                        success: function(r) {
                            $('#akhir_detail').html(r)
                        }
                    });
                });
                $(document).on('click', '.selesai', function() {
                    var id_cetak = $(this).attr('id_cetak');

                    $('.cetak').val(id_cetak);
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
