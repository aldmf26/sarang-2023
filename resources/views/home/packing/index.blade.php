<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>
        <a href="#" data-bs-toggle="modal" data-bs-target="#tambahPack" class="float-end btn btn-sm btn-primary"><i
                class="fas fa-plus"></i>Tambah</a>
        
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-4 mb-2 ">
                <table>
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table>
            </div>
            <div class="col-lg-12">
                <table class="table table-stripped" id="tablealdi">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tgl Kirim</th>
                            <th>No Nota</th>
                            <th>Nama Packing List</th>
                            <th class="text-end">Box</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gr</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packing as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td>{{ $d->no_nota }}</td>
                                <td>{{ ucwords($d->nm_packing) }}</td>
                                <td align="right">{{ $d->ttl_box }}</td>
                                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                <td align="right">{{ number_format($d->gr, 0) }}</td>
                                <td align="center">
                                    <button class="btn btn-sm btn-primary detail" no_nota="{{ $d->no_nota }}"
                                        type="button"><i class="fas fa-eye"></i></button>
                                    <a href="{{ route('packinglist.print', $d->no_nota) }}"
                                        class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-print"></i></a>
                                        
                                    <a onclick="return confirm('Yakin dihapus ?')" href="{{ route('packinglist.delete', $d->no_nota) }}"
                                        class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                        
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </section>


        <form action="{{ route('packinglist.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambahPack" title="Tambah Packing List" size="modal-lg">
                {{-- <div id="load_tbh"></div> --}}
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
                        tambahPengiriman: function(id_pengiriman, grade, pcs, gr, no_box) {
                            this.idPengiriman.push(id_pengiriman)
                            this.idPengirimanGrade.push({
                                grade: grade,
                                id_pengiriman: id_pengiriman
                            })
                            console.log(this.idPengirimanGrade)
                            let pengiriman = this.pengiriman
                            let selectedItem = this.selectedItem
                            let selectedPengiriman = this.selectedPengiriman
                            const index = pengiriman.findIndex(item => item.id_pengiriman === id_pengiriman);
                            if (index !== -1) {
                                let adaGrade = selectedItem.find(item => item.grade === grade);
                                if (adaGrade) {
                                    adaGrade.box += 1;
                                    adaGrade.pcs += pcs;
                                    adaGrade.gr += gr;
                                    adaGrade.id_pengiriman += `,${id_pengiriman}`;
                                } else {
                    
                                    selectedItem.push({
                                        grade: grade,
                                        box: 1,
                                        pcs: pcs,
                                        gr: gr,
                                        id_pengiriman: id_pengiriman
                                    });
                                }
                                {{-- ditambahkan ke array sementra pilih pengiriman --}}
                                selectedPengiriman.push({
                                    id_pengiriman: id_pengiriman,
                                    grade: grade,
                                    pcs: pcs,
                                    gr: gr,
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
                        <div class="col-lg-5">
                            <input id="pencarianTbh" type="text" class="form-control form-control-sm mb-2"
                                placeholder="cari">
                            <table id="tbl-aldi" class="table table-hover table-stripped">
                                <thead>
                                    <tr>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead">Pcs</th>
                                        <th class="dhead">Gr</th>
                                        <th class="dhead">No Barcode</th>
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
                        <div class="col-lg-7">
                            <h6 class="mb-3 pb-1 float-start">Pengiriman</h6>
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
                                            <td class="d-none">
                                                <input type="text" name="id_pengiriman[]"
                                                    :value="item.id_pengiriman">
                                            </td>
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

        <x-theme.modal btnSave="T" idModal="detail" title="Detail Packing list">
            <div id="loadDetail"></div>
        </x-theme.modal>
    </x-slot>
    @section('scripts')
        <script>
            pencarian('pencarian', 'tablealdi')
            pencarian('pencarianTbh', 'tbl-aldi')

            function loadTbh() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('packinglist.load_tbh') }}",
                    success: function(r) {
                        $('#load_tbh').html(r);
                        pencarian('pencarianTbh', 'tbl-aldi')
                    }
                });
            }

            loadTbh()
            $(document).on('click', '.detail', function(e) {
                e.preventDefault();

                const no_nota = $(this).attr('no_nota')
                $.ajax({
                    type: "GET",
                    url: "{{ route('packinglist.detail') }}?no_nota=" + no_nota,
                    success: function(r) {
                        $("#loadDetail").html(r);
                        $('#detail').modal('show')
                    }
                });
            })
        </script>
    @endsection
</x-theme.app>
