<x-theme.app title="{{ $title }}" table="Y" sizeCard="10" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }} </h6>
        </div>
        <div class="col-lg-12"><br>
            <hr style="border: 2px solid #435EBE">
        </div>


    </x-slot>


    <x-slot name="cardBody">
        
<style>
    .scrollable-table {
        max-height: 300px; /* Atur tinggi maksimum tabel */
        overflow-y: auto; /* Tampilkan scrollbar vertikal jika terlalu banyak baris */
        overflow-x: hidden; /* Sembunyikan scrollbar horizontal */
    }

    /* Optional: Atur lebar maksimum tabel jika ingin mengaktifkan scrollbar horizontal */
    .scrollable-table table {
        max-width: 100%;
    }
</style>
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
                                    class="form-control">
                            </td>
                            <td>
                                <input name="partai" type="text" value="" placeholder="partai bj"
                                    class="form-control">
                            </td>

                        </tr>
                    </table>
                </div>
            </section>
            <div class="row" x-data="{
                cetak: {{ json_encode($cetak) }},
                selectedItem: [],
                ttlPcs: 0,
                ttlGr: 0,
                tambah(id_cetak, tipe, pcs, gr, no_box) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
                    selectedItem.push({
                        id_cetak: id_cetak,
                        no_box: no_box,
                        tipe: tipe,
                        pcs_akhir: parseFloat(pcs),
                        gr_akhir: parseFloat(gr),
                    });
            
                    const index = cetak.findIndex(item => item.id_cetak === id_cetak);
                    cetak.splice(index, 1);
            
                    this.ttlPcs += pcs
                    this.ttlGr += gr
            
                },
                hapus(id_cetak) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
            
                    selectedItem.forEach((e) => {
                        if (e.id_cetak === id_cetak) {
                            cetak.push({
                                id_cetak: e.id_cetak,
                                no_box: e.no_box,
                                tipe: e.tipe,
                                pcs_akhir: parseFloat(e.pcs_akhir),
                                gr_akhir: parseFloat(e.gr_akhir),
                            });
                            const index = selectedItem.findIndex(item => item.id_cetak === e.id_cetak);
                            selectedItem.splice(index, 1);
            
                            this.ttlPcs -= e.pcs_akhir
                            this.ttlGr -= e.gr_akhir
                        }
                    })
            
            
                }
            }">
                <div class="col-lg-4">
                    <div class="scrollable-table">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(ctk, i) in cetak" :key="ctk.no_box">
                                <tr style="cursor: pointer"
                                    @click="tambah(ctk.id_cetak,ctk.tipe,ctk.pcs_akhir,ctk.gr_akhir, ctk.no_box)">
                                    <td x-text="ctk.tipe"></td>
                                    <td x-text="ctk.no_box"></td>
                                    <td align="right" x-text="ctk.pcs_akhir"></td>
                                    <td align="right" x-text="ctk.gr_akhir"></td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-4">
                    <div class="scrollable-table">
                    
                    <table class="table table-hover table-bordered">
                        <thead class="bg-success">
                            <tr>
                                <th class=" text-white">Ket</th>
                                <th class=" text-white">No Box</th>
                                <th class=" text-white text-end">Pcs</th>
                                <th class=" text-white text-end">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, i) in selectedItem" :key="item.no_box">
                                <tr @click="hapus(item.id_cetak)" style="cursor: pointer">
                                    <td class="d-none">
                                        <input type="text" name="no_box[]" :value="item.no_box">
                                        <input type="text" name="pcs_akhir[]" :value="item.pcs_akhir">
                                        <input type="text" name="gr_akhir[]" :value="item.gr_akhir">
                                    </td>
                                    <td x-text="item.tipe"></td>
                                    <td x-text="item.no_box"></td>
                                    <td align="right" x-text="item.pcs_akhir"></td>
                                    <td align="right" x-text="item.gr_akhir"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="col-lg-2">
                    <table class="table">
                        <tr>
                            <td width="70" align="left">
                                <h6>Ttl Pcs</h6>
                            </td>
                            <td>
                                <h6>:</h6>
                            </td>
                            <td>
                                <h6 x-text="ttlPcs">0</h6>
                            </td>
                        </tr>
                        <tr>
                            <td width="70" align="left">
                                <h6>Ttl Gr</h6>
                            </td>
                            <td>
                                <h6>:</h6>
                            </td>
                            <td>
                                <h6 x-text="ttlGr">0</h6>
                            </td>
                        </tr>
                    </table>
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
