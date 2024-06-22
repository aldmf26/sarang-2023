<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('packinglist.create') }}" method="post">
            @csrf
        <div class="row" x-data="{
            pengiriman: {{ $pengiriman }},
            selectedItem: [],
            selectedPengiriman: [],
            idPengiriman: [],
            idPengirimanGrade: [],
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
                if (index !== -1) {
                    const item = this.selectedItem[index];
                    this.selectedItem.splice(index, 1);
                    for (let i = this.selectedPengiriman.length - 1; i >= 0; i--) {
                        if (this.selectedPengiriman[i].grade === grade) {
                            const pengirimanIndex = this.pengiriman.findIndex(p => p.id_pengiriman === this.selectedPengiriman[i].id_pengiriman);
                            if (pengirimanIndex !== -1) {
                                this.pengiriman[pengirimanIndex].pcs += this.selectedPengiriman[i].pcs;
                                this.pengiriman[pengirimanIndex].gr += this.selectedPengiriman[i].gr;
                            } else {
                                this.pengiriman.push({
                                    id_pengiriman: this.selectedPengiriman[i].id_pengiriman,
                                    grade: this.selectedPengiriman[i].grade,
                                    pcs: this.selectedPengiriman[i].pcs,
                                    gr: this.selectedPengiriman[i].gr,
                                    no_box: this.selectedPengiriman[i].no_box,
                                });
                            }
                            this.selectedPengiriman.splice(i, 1);
                        }
                    }
                }
            },
        }">
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
                    <input readonly type="text" value="{{ strtoupper(auth()->user()->name) }}" name="pgws_cek"
                        class="form-control">
                </div>
            </div>
            <div class="col-lg-4">
                <img width="80" src="{{ asset('img/kulkas.png') }}" alt="">
                <input id="pencarianTbh" type="text" class="form-control form-control-sm mb-2" placeholder="cari">
                <div class="scrollable-table">
                    <table id="tbl-aldi" class="table table-hover table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th class="dheadstock">Grade</th>
                                <th class="dheadstock">No Barcode
                                    {{-- <span @click="allPengiriman" class="badge bg-danger">All</span> --}}
                                </th>
                                <th class="dheadstock">Pcs</th>
                                <th class="dheadstock">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(p,i) in pengiriman" :key="p.id_pengiriman">
                                <tr style="cursor: pointer"
                                    @click="tambahPengiriman(p.id_pengiriman,p.grade,p.pcs,p.gr, p.no_box)">
                                    <td x-text="p.grade"></td>
                                    <td class="d-flex justify-content-between">
                                        <span x-text="p.no_box"></span>
                                        <span class="badge bg-primary me-2"><i class="fas fa-plus"></i>
                                        </span>
                                    </td>
                                    <td x-text="p.pcs"></td>
                                    <td x-text="p.gr"></td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <img width="80" src="{{ asset('img/meja.png') }}" alt="">

                <input id="pencarianDipilih" type="text" class="form-control form-control-sm mb-2"
                    placeholder="cari">
                <div class="scrollable-table">
                    <table id="tbl-dipilih" class="table table-hover table-stripped">
                        <thead class="bg-success">
                            <tr>
                                <th class="dhead">Grade</th>
                                <th class="dhead">No Barcode</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item,index) in selectedPengiriman" :key="index">

                                <tr>
                                    <td style="display: none">
                                        <input type="hidden" name="id_pengiriman[]" :value="item.id_pengiriman">
                                    </td>
                                    <td x-text="item.grade"></td>
                                    <td x-text="item.no_box"></td>
                                    <td x-text="item.pcs"></td>
                                    <td x-text="item.gr"></td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
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
                                        @click="removeFromSelection(item.grade)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
                <button type="submit" class="btn btn-sm btn-primary float-end">Save</button>
            </div>
        </div>
        </form>
        @section('scripts')
            <script>
                pencarian('pencarianTbh', 'tbl-aldi')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
