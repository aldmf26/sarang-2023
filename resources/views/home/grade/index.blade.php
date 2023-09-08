<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="#" icon="fa-plus" modal="Y" idModal="tambah_target" addClass="float-end" teks="Target" />
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-bordered" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Box</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th class="text-end">Pcs Awal</th>
                        <th class="text-end">Gr Awal</th>
                        <th class="text-end">Pcs Akhir</th>
                        <th class="text-end">Gr Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grade as $no => $g)
                    <tr>
                        <td>{{$no+1}}</td>
                        <td>{{$g->no_box}}</td>
                        <td>{{date('d M y',strtotime($g->tgl))}}</td>
                        <td>{{$g->nama}}</td>
                        <td align="right">{{$g->pcs_awal}}</td>
                        <td align="right">{{$g->gr_awal}}</td>
                        <td align="right">{{$g->pcs_awal}}</td>
                        <td align="right">{{$g->gr_awal}}</td>
                        <td align="center">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                                class="btn btn-sm btn-primary detail" no_box="{{$g->no_box}}"><i
                                    class="fas fa-eye"></i></a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#akhir"
                                class="btn btn-sm btn-warning akhir" tgl_grade="{{$g->tgl}}"
                                no_box="{{$g->no_box}}">Grade</a>
                        </td>
                    </tr>
                    @endforeach


                </tbody>

            </table>
        </section>

        <form action="{{ route('grading.add_target') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah_target" size="modal-lg-max" title="tambah Target" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead">Nama Anak</th>
                                    <th class="dhead">Tgl Terima</th>
                                    <th class="dhead text-end" width="110">Pcs Awal</th>
                                    <th class="dhead text-end" width="110">Gr Awal</th>

                                    <th class="dhead">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="no_box[]" id="" class="select3 pilihBox" count="1">
                                            <option value="">Pilih Box</option>
                                            <option value="1002">1002</option>
                                            <option value="1003">1003</option>
                                            <option value="1004">1004</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="id_anak[]" id="" class="select3 pilihAnak" count="1">
                                            <option value="">Pilih Anak</option>
                                            @foreach ($anak as $d)
                                            <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                                                ({{ $d->kelas }}) {{ ucwords($d->nama) }}</option>
                                            @endforeach
                                        </select>
                                        {{-- <input type="hidden" class="setHargaSatuan1"> --}}
                                    </td>
                                    <td><input type="date" class="form-control" name="tgl[]"></td>
                                    <td>
                                        <input type="text" name="pcs_awal[]" class="form-control pcs_awal">
                                    </td>
                                    <td><input type="text" name="gr_awal[]" class="form-control"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            {{-- <tbody id="tbh_baris"> --}}
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
        <form action="{{ route('grading.add_target') }}" method="post">
            @csrf
            <x-theme.modal idModal="akhir" size="modal-lg" title="Grading" btnSave="Y">
                <div class="row">
                    <div class="col-lg-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead">Tgl Grading</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="no_box"></td>
                                    <td class="tgl_grading"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">Grade</th>
                                    <th class="dhead">Pcs</th>
                                    <th class="dhead">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="" id="" class="select3_grade ">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($tipe as $t)
                                            <option value="{{$t->id_tipe}}">{{$t->tipe}}</option>
                                            @endforeach
                                        </select>

                                        <input type="hidden" name="no_box">
                                    </td>
                                    <td><input type="text" class="form-control"></td>
                                    <td><input type="text" class="form-control"></td>
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






        @section('scripts')
        <script>
            $(".select3").select2()
            $(".select3_grade").select2({
                dropdownParent: $('#akhir .modal-content')
            })

            plusRow(1, 'tbh_baris', "grading/tbh_baris")

            $(document).on('click', '.akhir', function() {
              var no_box = $(this).attr('no_box');
              var tgl_grade = $(this).attr('tgl_grade');

                $('.no_box').text(no_box);
                $('.tgl_grading').text(tgl_grade);

                          
            });
        </script>
        @endsection
    </x-slot>
</x-theme.app>