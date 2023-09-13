<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="#" icon="fa-plus" modal="Y" idModal="tambah" addClass="float-end" teks="Target" />
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
                        <td align="right">{{empty($g->pcs_akhir) ? '0' : $g->pcs_akhir}}</td>
                        <td align="right">{{empty($g->gr_akhir) ? '0' : $g->gr_akhir}}</td>
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
            <x-theme.modal idModal="tambah" size="modal-lg-max" title="tambah Target" btnSave="Y">
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
                                            @foreach ($no_box as $n)
                                            <option value="{{$n->no_box}}">{{$n->no_box}}</option>
                                            @endforeach
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

        <form action="{{ route('grading.add_grading') }}" method="post">
            @csrf
            <x-theme.modal idModal="akhir" size="modal-lg-max" title="Grading" btnSave="Y">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead">Tgl Grading</th>
                                    <th class="dhead">Pcs Awal</th>
                                    <th class="dhead">Gr Awal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" name="no_box" class="box_input">
                                <tr>
                                    <td class="no_box"></td>
                                    <td class="tgl_grading"></td>
                                    <td class="pcs_awal"></td>
                                    <td class="gr_awal"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12"></div>
                    <div class="col-lg-6">
                        <h5 for="">Bentuk</h5>
                        <table class="table table-bordered">
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
                                        <select name="grade[]" id="" class="select3_grade ">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($tipe as $t)
                                            <option value="{{$t->id_tipe}}">{{$t->tipe}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control pcs" name="pcs[]" value="0"></td>
                                    <td><input type="text" class="form-control gr" name="gr[]" value="0"></td>
                                </tr>
                            </tbody>
                            <tbody id="tbh_baris_bentuk">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9">
                                        <button type="button" class="btn btn-block btn-lg tbh_baris_bentuk"
                                            style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                                            <i class="fas fa-plus"></i> Tambah Baris Baru
                                        </button>
                                    </th>
                                </tr>
                                <tr>
                                    <td class=""></td>
                                    <th class="text-center ">Pcs</th>
                                    <th class="text-center ">GR</th>
                                </tr>
                                <tr>
                                    <th class="">Total</th>
                                    <td class="ttl_pcs text-center"></td>
                                    <td class="ttl_gr text-center"></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                    <div class="col-lg-6">
                        <h5 for="">Turun Grade</h5>
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
                                        <select name="grade[]" id="" class="select3_grade ">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($tipe2 as $t)
                                            <option value="{{$t->id_tipe}}">{{$t->tipe}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control pcs" name="pcs[]" value="0"></td>
                                    <td><input type="text" class="form-control gr" name="gr[]" value="0"></td>
                                </tr>
                            </tbody>
                            <tbody id="tbh_baris_turun">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9">
                                        <button type="button" class="btn btn-block btn-lg tbh_baris_turun"
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

        <x-theme.modal idModal="detail" title="Detail Cabut" size="modal-lg" btnSave="T">
            <div class="row">
                <div class="col-lg-12">
                    <div id="load_detail_grading"></div>
                </div>
            </div>
        </x-theme.modal>






        @section('scripts')
        <script>
            $(".select3").select2()
            $(".select3_grade").select2({
                dropdownParent: $('#akhir .modal-content')
            })

            plusRow(1, 'tbh_baris', "grading/tbh_baris_target")

            plusRow2(1, 'tbh_baris_bentuk', "grading/tbh_baris")
            plusRow2(1, 'tbh_baris_turun', "grading/tbh_baris_turun")

            $(document).on('click', '.akhir', function() {
              var no_box = $(this).attr('no_box');
            
              $.ajax({
                type: "GET",
                url: "grading/load_grade",
                data: {
                    no_box:no_box,
                },
                dataType: "json",
                success: function (r) {
                    $(".no_box").text(r['no_box']);
                    $(".box_input").val(r['no_box']);
                    $(".tgl_grading").text(r['tgl']);
                    $(".pcs_awal").text(r['pcs']);
                    $(".gr_awal").text(r['gr']);
                }
                });

                          
            });
            $(document).on('keyup', '.pcs', function() {
              var pcs = $(this).val();

              var total_pcs = 0;
              $(".pcs").each(function() {
                total_pcs += parseFloat($(this).val());
              });

              
              $('.ttl_pcs').text(total_pcs);
            });
            $(document).on('keyup', '.gr', function() {
              var gr = $(this).val();

              var total_gr = 0;
              $(".gr").each(function() {
                total_gr += parseFloat($(this).val());
              });

              
              $('.ttl_gr').text(total_gr);
            });

            $(document).on('click', '.detail', function(){
                    var no_box = $(this).attr('no_box')
                    $.ajax({
                        type: "GET",
                        url: "grading/load_detail_grading",
                        data: {
                            no_box:no_box,
                        },
                        success: function (r) {
                            $("#load_detail_grading").html(r);
                        }
                    });
            })
        </script>
        @endsection
    </x-slot>
</x-theme.app>