<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>
        <a href="#" data-bs-toggle="modal" data-bs-target="#tambahPack" class="float-end btn btn-sm btn-primary"><i
                class="fas fa-plus"></i>Tambah Packinglist</a>
        <a href="{{ route('packinglist.add_box_kirim') }}" class="float-end btn btn-sm btn-primary me-2"><i
                class="fas fa-plus"></i>Tambah Box Kirim</a>
        <x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end"
            teks="Import" />
        <form action="{{ route('pengiriman.import') }}" enctype="multipart/form-data" method="post">
            @csrf
            <x-theme.modal size="modal-lg" idModal="import" title="Import Pengiriman">
                <div class="row">
                    <table>
                        <tr>
                            <td width="100" class="pl-2">
                                <img width="80px" src="{{ asset('/img/1.png') }}" alt="">
                            </td>
                            <td>
                                <span style="font-size: 20px;"><b> Download Excel template</b></span><br>
                                File ini memiliki kolom header dan isi yang sesuai dengan data menu
                            </td>
                            <td>
                                <a href="{{ route('pengiriman.template') }}" class="btn btn-primary btn-sm"><i
                                        class="fa fa-download"></i> DOWNLOAD
                                    TEMPLATE</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <hr>
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="pl-2">
                                <img width="80px" src="{{ asset('/img/2.png') }}" alt="">
                            </td>
                            <td>
                                <span style="font-size: 20px;"><b> Upload Excel template</b></span><br>
                                Setelah mengubah, silahkan upload file.
                            </td>
                            <td>
                                <input type="file" name="file" class="form-control">
                            </td>
                        </tr>
                    </table>
                </div>
            </x-theme.modal>
        </form>
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <style>
            thead {
                position: sticky;
                top: 0;
                background-color: #f1f1f1;
                /* Warna latar belakang header yang tetap */
                z-index: 1;
            }
        </style>
        <section class="row">
           

            <div class="col-lg-12">
                <hr>
            </div>
            <div class="col-lg-8">
                {{-- @include('home.packing.nav', ['name' => 'index']) --}}
            </div>
            <div class="col-lg-4 mb-2 ">
                <table>
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table>
            </div>
            @if ($kategori == 'packing')
                @include('home.packing.tbl_index_packing')
            @else
                @include('home.packing.tbl_index_pengiriman')
            @endif
        </section>

        <form action="{{ route('packinglist.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambahPack" title="Tambah Packing List" size="modal-lg-max">
                <div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Tanggal Packing List</label>
                                <input type="date" name="tgl" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Nama Packing List</label>
                                <input required type="text" name="nm_packing" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Pgws</label>
                                <input readonly type="text" value="{{ strtoupper(auth()->user()->name) }}"
                                    name="pgws_cek" class="form-control">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" x-data="{
                        pengiriman: {{ $pengiriman }},
                        selectedItem: [],
                        selectedPengiriman: [],
                        idPengiriman: [],
                        idPengirimanGrade: [],
                        allPengiriman: function() {
                            this.pengiriman.forEach(item => {
                                console.log(item)
                                this.tambahPengiriman(item.id_pengiriman, item.grade, item.pcs_akhir, item.gr_akhir, item.no_box);
                            });
                        },
                        tambahPengiriman: function(id_pengiriman, grade, pcs, gr, no_box) {
                            this.idPengiriman.push(id_pengiriman)
                    
                            console.log(this.idPengirimanGrade)
                            let pengiriman = this.pengiriman
                            let selectedItem = this.selectedItem
                            let selectedPengiriman = this.selectedPengiriman
                            const index = pengiriman.findIndex(item => item.id_pengiriman === id_pengiriman);
                            if (index !== -1) {
                                let adaGrade = selectedItem.find(item => item.grade === grade);
                                if (adaGrade) {
                                    adaGrade.box += 1;
                                    adaGrade.pcs += parseFloat(pcs);
                                    adaGrade.gr += parseFloat(gr);
                    
                                    if (!Array.isArray(adaGrade.id_pengiriman)) {
                                        adaGrade.id_pengiriman = []; // Initialize as an array if not already
                                    }
                    
                                    if (!adaGrade.id_pengiriman.includes(id_pengiriman)) {
                                        adaGrade.id_pengiriman.push(id_pengiriman);
                                    }
                    
                                    {{-- if (!adaGrade.id_pengiriman.includes(id_pengiriman)) {
                                        adaGrade.id_pengiriman += `,${id_pengiriman}`;
                                    } --}}
                                    {{-- adaGrade.id_pengiriman += `,${id_pengiriman}`; --}}
                                } else {
                    
                                    selectedItem.push({
                                        grade: grade,
                                        box: 1,
                                        pcs: parseFloat(pcs),
                                        gr: parseFloat(gr),
                                        id_pengiriman: id_pengiriman
                                    });
                                }
                                {{-- ditambahkan ke array sementra pilih pengiriman --}}
                                selectedPengiriman.push({
                                    id_pengiriman: id_pengiriman,
                                    grade: grade,
                                    pcs: parseFloat(pcs),
                                    gr: parseFloat(gr),
                                    no_box: no_box,
                                })
                                pengiriman.splice(index, 1);
                            }
                    
                        },
                        removeFromSelection: function(grade) {
                            const indexGrade = this.idPengirimanGrade.findIndex(item => item.grade === grade);
                            if (indexGrade !== -1) {
                                this.idPengirimanGrade.splice(indexGrade, 1);
                            }
                    
                            const index = this.selectedItem.findIndex(item => item.grade === grade);
                            this.selectedItem.splice(index, 1);
                            const selectedPengiriman = this.selectedPengiriman
                            for (let i = this.selectedPengiriman.length - 1; i >= 0; i--) {
                                if (this.selectedPengiriman[i].grade === grade) {
                                    this.pengiriman.push({
                                        id_pengiriman: this.selectedPengiriman[i].id_pengiriman,
                                        grade: this.selectedPengiriman[i].grade,
                                        pcs_akhir: this.selectedPengiriman[i].pcs,
                                        gr_akhir: this.selectedPengiriman[i].gr,
                                        no_box: this.selectedPengiriman[i].no_box,
                                    });
                                    this.selectedPengiriman.splice(i, 1);
                                }
                            }
                        },
                    }">
                        <div class="col-lg-3">
                            <img width="80" src="{{ asset('img/kulkas.png') }}" alt="">
                            <input id="pencarianTbh" type="text" class="form-control form-control-sm mb-2"
                                placeholder="cari">
                            <div class="scrollable-table">
                                <table id="tbl-aldi" class="table table-hover table-stripped">
                                    <thead>
                                        <tr>
                                            <th class="dhead">Grade</th>
                                            <th class="dhead">Pcs</th>
                                            <th class="dhead">Gr</th>
                                            <th class="dhead">No Barcode <span @click="allPengiriman"
                                                    class="badge bg-danger">All</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(p,i) in pengiriman" :key="p.id_pengiriman">
                                            <tr style="cursor: pointer"
                                                @click="tambahPengiriman(p.id_pengiriman,p.grade,p.pcs_akhir,p.gr_akhir, p.no_box)">
                                                <td x-text="p.grade"></td>
                                                <td x-text="p.pcs_akhir"></td>
                                                <td x-text="p.gr_akhir"></td>
                                                <td x-text="p.no_box"></td>
                                            </tr>
                                        </template>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <img width="80" src="{{ asset('img/meja.png') }}" alt="">

                            <input id="pencarianDipilih" type="text" class="form-control form-control-sm mb-2"
                                placeholder="cari">
                            <div class="scrollable-table">
                                <table id="tbl-dipilih" class="table table-hover table-stripped">
                                    <thead class="bg-success">
                                        <tr>
                                            <th class="text-white">Grade</th>
                                            <th class="text-white">Pcs</th>
                                            <th class="text-white">Gr</th>
                                            <th class="text-white">No Barcode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item,index) in selectedPengiriman" :key="index">

                                            <tr>
                                                <td style="display: none">
                                                    <input type="hidden" name="id_pengiriman[]"
                                                        :value="item.id_pengiriman">
                                                </td>
                                                <td x-text="item.grade"></td>
                                                <td x-text="item.pcs"></td>
                                                <td x-text="item.gr"></td>
                                                <td x-text="item.no_box"></td>
                                            </tr>
                                        </template>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img width="80" src="{{ asset('img/box.png') }}" alt="">

                            <h6 class="mb-3 pb-1">Pengiriman</h6>
                            {{-- <input type="text" name="id_pengiriman" :value="idPengiriman"> --}}

                            <table class="table table-hover table-stripped">
                                <thead>
                                    <tr>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead">Box</th>
                                        <th class="dhead">Pcs</th>
                                        <th class="dhead">Gram</th>
                                        <th class="dhead">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item,index) in selectedItem" :key="index">

                                        <tr>

                                            <td x-text="item.grade"></td>
                                            <td x-text="item.box"></td>
                                            <td x-text="item.pcs"></td>
                                            <td x-text="item.gr"></td>
                                            <td>
                                                <button type="button" class="badge bg-danger badge-sm"
                                                    @click="removeFromSelection(item.grade)"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </template>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal btnSave="T" size="modal-lg" idModal="detail" title="Detail Packing list">
            <div id="loadDetail"></div>
        </x-theme.modal>
    </x-slot>
    @section('scripts')
        <script>
            pencarian('pencarian', 'tablealdi')
            pencarian('pencarianTbh', 'tbl-aldi')
            pencarian('pencarianDipilih', 'tbl-dipilih')

            $(document).on('click', '.detail', function(e) {
                e.preventDefault();

                const no_nota = $(this).attr('no_nota')
                $.ajax({
                    type: "GET",
                    url: "{{ route('packinglist.detail') }}?no_nota=" + no_nota,
                    success: function(r) {
                        $("#loadDetail").html(r);
                        $('#detail').modal('show')
                        pencarian('pencarianSum', 'tbl-sum')
                        pencarian('pencarianList', 'tbl-list')
                    }
                });
            })
        </script>
    @endsection
</x-theme.app>
