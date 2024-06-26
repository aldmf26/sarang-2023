<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-12 mb-3">
                @include('home.gradingbj.navsiapgrade', ['name' => 'index'])
            </div>
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-12"><br>
                <hr style="border: 2px solid #435EBE">
            </div>
        </div>

    </x-slot>
    <x-slot name="cardBody">
        <div class="row" x-data="{ tbhSuntik: false, baris: 1 }">
            <h6 @click.prevent="tbhSuntik = ! tbhSuntik"><a href="#">Tmbh Suntikan <i class="fas fa-plus"></i></a>
            </h6>

            <div class="col-lg-8" x-show="!tbhSuntik">
                <div class="d-flex mb-3 flex-row align-items-center">
                    <label for="">Baris</label>
                    <input x-model="baris" type="text" class="form-control ms-2" style="width: 60px">
                </div>
                <form action="{{ route('gradingbj.create_suntikan') }}" method="post">
                    @csrf
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Partai</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead" width="80">Pcs</th>
                                <th class="dhead" width="80">Gr</th>
                                <th class="dhead">Ttl Rp</th>
                                <th class="dhead">Cost Cabut</th>
                                <th class="dhead">Cost Cetak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="d in Array.from({length:baris})">
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="nm_partai[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="tipe[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="no_box[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="pcs[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="gr[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="ttl_rp[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="cost_cabut[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="cost_cetak[]">
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                    <button class="btn btn-sm btn-primary btn-block">Simpan</button>
                </form>
            </div>
        </div>
        <hr>
        <form action="{{ route('gradingbj.create') }}" method="post">
            @csrf
            <section class="row">

                <div class="col-lg-5">
                    <table class="table">
                        <tr>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">Tipe</th>
                            <th class="dhead">Partai BJ</th>
                        </tr>
                        <tr>
                            <td>
                                <input name="tgl" type="date" value="{{ date('Y-m-d') }}" class="form-control">
                            </td>
                            <td>
                                <input name="ket" type="text" value="" placeholder="ket"
                                    class="form-control" required>
                            </td>
                            <td>
                                <input name="partai" type="text" value="" placeholder="partai bj"
                                    class="form-control" required>
                            </td>

                        </tr>
                    </table>
                </div>
            </section>
            <div class="row" x-data="{
                cetak: {{ json_encode($cetak) }},
                cabut: {{ json_encode($cabut_selesai) }},
                suntikan: {{ json_encode($suntikan) }},
                selectedItem: [],
                ttlPcs: 0,
                ttlGr: 0,
                ttlRp: 0,
                ttlCostCabut: 0,
                ttlCostCetak: 0,
                numberFormat(value) {
                    return parseFloat(value).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
                },
                tambah(id_cetak, tipe, pcs, gr, no_box, total_rp, cost_cabut, cost_cetak) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
                    const cabut = this.cabut
                    const suntikan = this.suntikan
            
                    selectedItem.push({
                        id_cetak: id_cetak,
                        no_box: no_box,
                        tipe: tipe,
                        pcs_akhir: parseFloat(pcs),
                        gr_akhir: parseFloat(gr),
                        ttl_rp: parseFloat(total_rp),
                        cost_cabut: parseFloat(cost_cabut),
                        cost_cetak: parseFloat(cost_cetak),
                    });
            
                    const index = cetak.findIndex(item => item.id_cetak === id_cetak);
                    cetak.splice(index, 1);
            
                    const index2 = cabut.findIndex(item => item.id_gudang_ctk === id_cetak);
                    cabut.splice(index2, 1);
            
                    const index3 = suntikan.findIndex(item => item.id_suntikan === id_cetak);
                    suntikan.splice(index3, 1);
            
                    this.ttlPcs += parseFloat(pcs)
                    this.ttlGr += parseFloat(gr)
                    this.ttlRp += parseFloat(total_rp)
                    this.ttlCostCabut += parseFloat(cost_cabut)
                    this.ttlCostCetak += parseFloat(cost_cetak)
            
                },
                hapus(id_cetak) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
                    const cabut = this.cabut
            
                    selectedItem.forEach((e) => {
                        if (e.id_cetak === id_cetak) {
                            cetak.push({
                                id_cetak: e.id_cetak,
                                no_box: e.no_box,
                                tipe: e.tipe,
                                pcs_akhir: parseFloat(e.pcs_akhir),
                                gr_akhir: parseFloat(e.gr_akhir),
                                ttl_rp: parseFloat(e.total_rp),
                                cost_cabut: parseFloat(e.cost_cabut),
                                cost_cetak: parseFloat(e.cost_cetak),
                            });
                            const index = selectedItem.findIndex(item => item.id_cetak === e.id_cetak);
                            selectedItem.splice(index, 1);
            
                            this.ttlPcs -= e.pcs_akhir
                            this.ttlGr -= e.gr_akhir
                            this.ttlRp -= e.ttl_rp
                            this.ttlCostCabut -= e.cost_cabut
                            this.ttlCostCetak -= e.cost_cetak
                        }
                    })
            
            
                }
            }">
                <div class="col-lg-6">
                    <input id="inputPencarian" type="text" class="form-control form-control-sm mb-1" placeholder="pencarian...">
                    <div class="scrollable-table">
                        <table class="table table-hover table-bordered" id="tblPencarian">
                            <thead>
                                <tr>
                                    <th class="dhead">Tipe</th>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    <th class="dhead text-end">Ttl Rp</th>
                                    <th class="dhead text-end">Cost Cabut</th>
                                    <th class="dhead text-end">Cost Cetak</th>
                                    <th class="dhead text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(ctk, i) in cetak" :key="ctk.no_box">
                                    <tr style="cursor: pointer"
                                        @click="tambah(ctk.id_cetak,ctk.tipe,ctk.pcs_akhir,ctk.gr_akhir, ctk.no_box,ctk.total_rp,ctk.cost_cabut,ctk.cost_cetak)">
                                        <td x-text="ctk.tipe"></td>
                                        <td x-text="ctk.no_box"></td>
                                        <td align="right" x-text="(parseInt(ctk.pcs_akhir) || 0) ">
                                        </td>
                                        <td align="right" x-text="(parseInt(ctk.gr_akhir) || 0) ">
                                        </td>
                                        <td align="right" x-text="numberFormat((parseInt(ctk.total_rp) || 0))">
                                        </td>
                                        <td align="right" x-text="numberFormat(ctk.cost_cabut)"></td>
                                        <td align="right" x-text="numberFormat(ctk.cost_cetak)"></td>
                                        <td class="text-center"><a href="javascript:void(0)"
                                                class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(item, index) in cabut" :key="index">
                                    <tr style="cursor: pointer"
                                        @click="tambah(item.id_gudang_ctk,item.tipe,item.pcs_cabut,item.gr_cabut,item.no_box,item.ttl_rp,item.cost_cabut,0)">
                                        >
                                        <td x-text="item.tipe"></td>
                                        <td x-text="item.no_box"></td>
                                        <td align="right" x-text="numberFormat(item.pcs_cabut)"></td>
                                        <td align="right" x-text="numberFormat(item.gr_cabut)"></td>
                                        <td align="right" x-text="numberFormat(item.ttl_rp)">ds</td>
                                        <td align="right" x-text="numberFormat(item.cost_cabut)"></td>
                                        <td align="right">0</td>
                                        <td class="text-center"><a href="javascript:void(0)"
                                                class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
                                        </td>
                                    </tr>

                                </template>
                                <template x-for="(item, index) in suntikan" :key="index">
                                    <tr style="cursor: pointer"
                                        @click="tambah(
                                            item.id_suntikan,
                                            item.tipe,
                                            item.pcs,
                                            item.gr,
                                            item.no_box,
                                            item.ttl_rp,
                                            item.cost_cabut,
                                            item.cost_cetak
                                        )">
                                        >
                                        <td x-text="item.tipe"></td>
                                        <td x-text="item.no_box"></td>
                                        <td align="right" x-text="numberFormat(item.pcs)"></td>
                                        <td align="right" x-text="numberFormat(item.gr)"></td>
                                        <td align="right" x-text="numberFormat(item.ttl_rp)"></td>
                                        <td align="right" x-text="numberFormat(item.cost_cabut)"></td>
                                        <td align="right" x-text="numberFormat(item.cost_cetak)"></td>
                                        <td class="text-center"><a href="javascript:void(0)"
                                                class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
                                        </td>
                                    </tr>

                                </template>


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="scrollable-table">

                        <table class="table table-hover table-bordered">
                            <thead class="bg-success">
                                <tr>
                                    <th class=" text-white">Tipe</th>
                                    <th class=" text-white">No Box</th>
                                    <th class=" text-white text-end">Pcs</th>
                                    <th class=" text-white text-end">Gr</th>
                                    <th class=" text-white text-end">Ttl Rp</th>
                                    <th class=" text-white text-end">Cost Cabut</th>
                                    <th class=" text-white text-end">Cost Cetak</th>
                                    <th class=" text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in selectedItem" :key="item.no_box">
                                    <tr @click="hapus(item.id_cetak)" style="cursor: pointer">
                                        <td class="d-none">
                                            <input type="text" name="no_box[]" :value="item.no_box">
                                            <input type="text" name="pcs_akhir[]" :value="item.pcs_akhir">
                                            <input type="text" name="gr_akhir[]" :value="item.gr_akhir">
                                            <input type="text" name="ttl_rp[]" :value="item.ttl_rp">
                                            <input type="text" name="cost_cabut[]" :value="item.cost_cabut">
                                            <input type="text" name="cost_cetak[]" :value="item.cost_cetak">
                                            <input type="text" name="tipe[]" :value="item.tipe">
                                        </td>
                                        <td x-text="item.tipe"></td>
                                        <td x-text="item.no_box"></td>
                                        <td align="right"
                                            x-text="(parseInt(item.pcs_akhir) || 0) + (parseInt(item.pcs_cabut) || 0)">
                                        </td>
                                        <td align="right"
                                            x-text="(parseInt(item.gr_akhir)|| 0) + (parseInt(item.gr_cabut) || 0)">
                                        </td>
                                        <td align="right" x-text="numberFormat(item.ttl_rp)"></td>
                                        <td align="right" x-text="numberFormat(item.cost_cabut)"></td>
                                        <td align="right" x-text="numberFormat(item.cost_cetak)"></td>
                                        <td class="text-center"><a href="javascript:void(0)"
                                                class="btn btn-danger btn-sm"><i class="fas fa-minus"></i></a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>

                                    <th class="text-end" x-text="ttlPcs"></th>
                                    <th class="text-end" x-text="ttlGr"></th>
                                    <th class="text-end" x-text="numberFormat(ttlRp)"></th>
                                    <th class="text-end" x-text="numberFormat(ttlCostCabut)"></th>
                                    <th class="text-end" x-text="numberFormat(ttlCostCetak)"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
            </div>

    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>

        <a href="{{ route('gradingbj.index') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>


    </x-slot>
    @section('scripts')
        <script>
            pencarian('inputPencarian','tblPencarian')
            $('.select2cek').select2()

            function tekanBawah() {
                function navigateInputs(className, e) {
                    if (e.keyCode === 40 || e.keyCode === 38) {
                        e.preventDefault();
                        var $input = $(e.target);
                        var currentCount = parseInt($input.attr('count'));

                        var direction = (e.keyCode === 40) ? 1 : (e.keyCode === 38) ? -1 :
                            0; // 1 for down arrow, -1 for up arrow

                        var $nextInput = $input.closest('tr').siblings('tr').find('.' + className + '[count="' + (currentCount +
                            direction) + '"]');

                        if ($nextInput.length) {
                            $nextInput.focus();

                            // Memeriksa jika kelas yang di-fokus adalah pcsAwal atau grAwal, lalu memilih seluruh teks di dalamnya.
                            if ($nextInput.hasClass('pcs') || $nextInput.hasClass('gr')) {
                                $nextInput.select(); // Memilih seluruh teks dalam input.
                            }
                        }
                    }
                }

                $('table').on('keydown', 'input', function(e) {
                    var className = $(this).attr('class').split(' ')[
                        1]; // Mendapatkan kelas kedua (contoh: 'nolot' atau 'nobox')
                    navigateInputs(className, e);
                });
            }
            tekanBawah()

            function keyupBp(kelas, ditambah = false) {
                $('.' + kelas).on('keyup', function() {
                    var currentCount = $(this).attr('count');
                    var currentValue = $(this).val();
                    var hasil = 0

                    var shouldUpdate = false;
                    $('.' + kelas).each(function() {
                        var count = $(this).attr('count');

                        if (shouldUpdate) {
                            if (ditambah) {
                                currentValue++
                                hasil = parseFloat(currentValue);
                            } else {
                                hasil = currentValue
                            }
                            $(this).val(hasil);
                        }
                        if (count === currentCount) {
                            shouldUpdate = true;
                        }
                    });
                });
            }

            keyupBp('partai')
            keyupBp('ket')
            keyupBp('gr')
        </script>
    @endsection
</x-theme.app>
