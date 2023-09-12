<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button idModal="tambah" modal="Y" href="#" icon="fa-plus" addClass="float-end" teks="Tambah" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th class="dhead">#</th>
                        <th class="dhead">No Box</th>
                        <th class="dhead">Kelas</th>
                        <th class="dhead">Nama Anak</th>
                        <th class="dhead">Tgl Ambil ~ Serah</th>
                        <th class="dhead" class="text-end">Gr EO Awal ~ Akhir</th>
                        <th class="dhead text-end">Susut</th>
                        <th class="dhead text-end">Ttl Rp</th>
                        <th class="dhead">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eo as $no => $d)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $d->no_box }}</td>
                            <td>{{ $d->kelas }}</td>
                            <td>{{ $d->nama }}</td>
                            <td>{{ $d->tgl_ambil }} ~ {{ $d->tgl_serah }}</td>
                            <td align="right">{{ $d->gr_eo_awal }} ~ {{ $d->gr_eo_akhir }}</td>
                            @php
                                $susut = empty($d->gr_eo_akhir) ? 0 : (1 - $d->gr_eo_akhir / $d->gr_eo_awal) * 100;
                            @endphp
                            <td align="right">{{ number_format($susut,0) }}%</td>
                            <td align="right">{{ number_format($d->ttl_rp,0)}} </td>
                            <td align="center">
                                @if ($d->selesai == 'T')
                                    <a class="btn btn-warning btn-sm inputAkhir" href="#"
                                        no_box="{{ $d->no_box }}" id_anak="{{ $d->id_anak }}" href="#"
                                        data-bs-toggle="modal" data-bs-target="#inputAkhir"></i>Akhir</a>

                                    @if (!empty($d->gr_akhir))
                                        <a class="btn btn-primary btn-sm selesai" href="#"
                                            id_cabut="{{ $d->id_eo }}" href="#" data-bs-toggle="modal"
                                            data-bs-target="#selesai"></i>Selesai</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        <form action="{{ route('eo.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="Tambah Stok" size="modal-lg" btnSave="Y">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead">Pengawas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="no_box" class="form-control select2 pilihBox" id="">
                                            <option value="">- Pilih No Box -</option>
                                            @foreach ($nobox as $d)
                                                <option value="{{ $d->no_box }}">{{ $d->no_box }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" readonly value="{{ auth()->user()->name }}" name="example" class="form-control">
                                        <input type="hidden" name="id_pengawas" readonly value="{{ auth()->user()->id }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="dhead">Tgl Ambil</th>
                            <th class="dhead" width="40%">Nama Anak</th>
                            <th class="dhead text-end">Gr EO Awal</th>
                            <th class="dhead" width="20%">Paket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="date" value="{{ date('Y-m-d') }}" name="tgl_ambil[]" class="form-control">
                            </td>
                          
                            <td>
                                <select required name="id_anak[]" class="form-control select2" id="">
                                    <option value="">- Pilih Anak -</option>
                                    @foreach ($anak as $d)
                                        <option value="{{ $d->id_anak }}">{{ strtoupper($d->nama) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input name="gr_eo_awal[]" type="text" class="form-control text-end" value="0">
                            </td>
                          
                            <td>
                                <select required name="id_kelas[]" id="" class="form-control">
                                    <option value="">- Pilih Kelas -</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id_kelas }}">{{ strtoupper($k->kelas) }}</option>
                                    @endforeach
                                </select>
                            </td>
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
               
            </x-theme.modal>
        </form>

        <form action="{{ route('eo.input_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut akhir" size="modal-lg" btnSave="Y">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form>

    </x-slot>
    @section('scripts')
    <script>
            plusRow(1, 'tbh_baris', "eo/tbh_baris")
            $(document).on('click', '.inputAkhir', function() {
                    var no_box = $(this).attr('no_box')
                    var id_anak = $(this).attr('id_anak')
                    $.ajax({
                        type: "GET",
                        url: "eo/load_modal_akhir",
                        data: {
                            no_box: no_box,
                            id_anak: id_anak,
                        },
                        success: function(r) {
                            $("#load_modal_akhir").html(r);
                        }
                    });
                })
    </script>
    @endsection
</x-theme.app>