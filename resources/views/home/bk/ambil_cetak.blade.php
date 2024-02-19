<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }} Ambil {{ ucwords($kategori) }}</h6>
        </div>
        <div class="col-lg-12"><br>
            <hr style="border: 2px solid #435EBE">
        </div>

        @include('home.bk.nav', ['name' => 'add'])

    </x-slot>


    <x-slot name="cardBody">
        @php
            $tblBk = DB::table('bk')->where('kategori', 'cetak')->pluck('no_box')->toArray();
            $get = Http::get('https://gudangsarang.ptagafood.com/api/apibk/bkCetakApi');
            $data = json_decode($get->getBody()); // Convert JSON to stdClass objects

            // Filter the results based on "no_box" values from $tblBk
            $filteredData = array_filter((array) $data, function ($item) use ($tblBk) {
                return !in_array($item->no_box, $tblBk);
            });

            // Reindex the array to remove string keys
            $filteredData = array_values($filteredData);

            // Convert the filtered data to JSON
            $result = json_encode($filteredData);
        @endphp

        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                font-size: 12px;
                width: 120px !important;
            }

            .scrollable-table {
                max-height: 300px;
                /* Atur tinggi maksimum tabel */
                overflow-y: auto;
                /* Tampilkan scrollbar vertikal jika terlalu banyak baris */
                overflow-x: hidden;
                /* Sembunyikan scrollbar horizontal */
            }

            /* Optional: Atur lebar maksimum tabel jika ingin mengaktifkan scrollbar horizontal */
            .scrollable-table table {
                max-width: 100%;
            }

            thead {
                position: sticky;
                top: 0;
                background-color: #f1f1f1;
                /* Warna latar belakang header yang tetap */
                z-index: 1;
            }
        </style>

        <form action="{{ route('bk.create_ambil_cetak') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-5">
                    <table class="table">
                        <tr>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">Penerima</th>
                            <th class="dhead">Admin</th>
                        </tr>
                        <tr>
                            <td>
                                <input name="tgl" type="date" value="{{ date('Y-m-d') }}" class="form-control">
                            </td>
                            <td>
                                <select pilihan="pengawas" name="penerima" id=""
                                    class="select3 selectPengawas selectTipe">
                                    <option value="">Pilih Pengawas</option>
                                    @foreach ($pengawas as $p)
                                        <option {{ $p->name == 'Tiyah' ? 'selected' : '' }} value="{{ $p->id }}">
                                            {{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input name="admin" type="text" readonly value="{{ auth()->user()->name }}"
                                    class="form-control">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>



            <div class="row" x-data="{
                cetak: {{ $result }},
                selectedItem: [],
                ttlPcs: 0,
                ttlGr: 0,
                ttlRp: 0,
                numberFormat(value) {
                    return parseFloat(value).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                },
                tambah(id_gudang_ctk, partai_h, tipe, pcs, gr, no_box, ttl_rp) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
                    selectedItem.push({
                        id_gudang_ctk: id_gudang_ctk,
                        no_box: no_box,
                        partai_h: partai_h,
                        tipe: tipe,
                        pcs_timbang_ulang: parseFloat(pcs),
                        gr_timbang_ulang: parseFloat(gr),
                        ttl_rp: parseFloat(ttl_rp),
                    });
            
                    const index = cetak.findIndex(item => item.id_gudang_ctk === id_gudang_ctk);
                    cetak.splice(index, 1);
            
                    this.ttlPcs += parseFloat(pcs)
                    this.ttlGr += parseFloat(gr)
                    this.ttlRp += parseFloat(ttl_rp)
            
                },
                hapus(id_gudang_ctk) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
            
                    selectedItem.forEach((e) => {
                        if (e.id_gudang_ctk === id_gudang_ctk) {
                            cetak.push({
                                id_gudang_ctk: e.id_gudang_ctk,
                                no_box: e.no_box,
                                partai_h: e.partai_h,
                                tipe: e.tipe,
                                pcs_timbang_ulang: parseFloat(e.pcs_timbang_ulang),
                                gr_timbang_ulang: parseFloat(e.gr_timbang_ulang),
                                ttl_rp: parseFloat(e.ttl_rp),
                            });
                            const index = selectedItem.findIndex(item => item.id_gudang_ctk === e.id_gudang_ctk);
                            selectedItem.splice(index, 1);
            
                            this.ttlPcs -= parseFloat(e.pcs_timbang_ulang)
                            this.ttlGr -= parseFloat(e.gr_timbang_ulang)
                            this.ttlRp -= parseFloat(e.ttl_rp)
                        }
                    })
            
            
                }
            }">
                <div class="col-lg-6">
                    <div class="scrollable-table">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="dhead">Partai H</th>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead">Tipe</th>
                                    <th class="dhead text-end">Pcs Tmbng Ulng</th>
                                    <th class="dhead text-end">Gr Tmbng Ulng</th>
                                    <th class="dhead text-end">Ttl Rp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(ctk, i) in cetak" :key="ctk.no_box">
                                    <tr style="cursor: pointer"
                                        @click="tambah(
                                            ctk.id_gudang_ctk,
                                            ctk.partai_h,
                                            ctk.tipe,
                                            ctk.pcs_timbang_ulang,
                                            ctk.gr_timbang_ulang,
                                            ctk.no_box,
                                            ctk.ttl_rp,
                                        )
                                        ">
                                        <td x-text="ctk.partai_h"></td>
                                        <td x-text="ctk.no_box"></td>
                                        <td x-text="ctk.tipe"></td>
                                        <td align="right" x-text="ctk.pcs_timbang_ulang"></td>
                                        <td align="right" x-text="ctk.gr_timbang_ulang"></td>
                                        <td align="right" x-text="numberFormat(ctk.ttl_rp)"></td>
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
                                    <th class=" text-white">Partai H</th>
                                    <th class=" text-white">No Box</th>
                                    <th class=" text-white">Tipe</th>
                                    <th class=" text-white text-end">Pcs Tmbng Ulng</th>
                                    <th class=" text-white text-end">Gr Tmbng Ulng</th>
                                    <th class=" text-white text-end">Ttl Rp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in selectedItem" :key="item.no_box">
                                    <tr @click="hapus(item.id_gudang_ctk)" style="cursor: pointer">
                                        <td class="d-none">
                                            <input type="text" name="partai_h[]" :value="item.partai_h">
                                            <input type="text" name="tipe[]" :value="item.tipe">
                                            <input type="text" name="no_box[]" :value="item.no_box">
                                            <input type="text" name="pcs_akhir[]" :value="item.pcs_timbang_ulang">
                                            <input type="text" name="gr_akhir[]" :value="item.gr_timbang_ulang">
                                            <input type="text" name="ttl_rp[]" :value="item.ttl_rp">
                                        </td>
                                        <td x-text="item.partai_h"></td>
                                        <td x-text="item.no_box"></td>
                                        <td x-text="item.tipe"></td>
                                        <td align="right" x-text="item.pcs_timbang_ulang"></td>
                                        <td align="right" x-text="item.gr_timbang_ulang"></td>
                                        <td align="right" x-text="numberFormat(item.ttl_rp)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-end" x-text="ttlPcs"></th>
                                    <th class="text-end" x-text="ttlGr"></th>
                                    <th class="text-end" x-text="numberFormat(ttlRp)"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- <div class="col-lg-2">
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
                </div> --}}
            </div>



    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('bk.index', ['kategori' => 'cetak']) }}"
            class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>
    @section('scripts')
        <script>
            $(".select3").select2()
            $('.selectPengawas').select2(); // Menginisialisasi semua elemen dengan kelas .selectPengawas sebagai Select2
        </script>
    @endsection
</x-theme.app>
