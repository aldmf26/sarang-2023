<x-theme.app title="{{ $title }}" table="T" cont="container-fluid">
    <x-slot name="slot">
        <div x-data="{
            cek: [],
            cekPrint: [],
            selectedItem: [],
            tambah(no_box, grade, pcs, gr) {
                const selectedItem = this.selectedItem
                const index = selectedItem.findIndex(item => item.no_box === no_box);
                if (index === -1) {
                    selectedItem.push({
                        no_box: no_box,
                        grade: grade,
                        pcs: parseFloat(pcs),
                        gr: parseFloat(gr),
                    });
                } else {
                    selectedItem.splice(index, 1);
                }
        
            },
        }">
            <div class="d-flex justify-content-between mb-3 gap-1">
                <h6>{{ $title }}</h6>
                <div class="d-flex gap-1">
                    {{-- <a class="btn btn-sm btn-primary"
                        href="{{ route('cabut.export_gudang', ['bulan' => $bulan, 'tahun' => $tahun, 'id_user' => $id_user]) }}"><i
                            class="fas fa-print"></i> Export All</a> --}}
                    <x-theme.button href="#" icon="fa-plus" variant="info" modal="Y" idModal="tambah"
                        teks="serah" />
                    <x-theme.button href="{{ route('gudangsarang.invoice_wip', ['kategori' => 'wip']) }}"
                        icon="fa-clipboard-list" teks="Po Wip" />
                    <div>
                        <form action="{{ route('pengiriman.kirim') }}" method="post">
                            @csrf
                            <input type="hidden" name="no_box" class="form-control" :value="cekPrint.join(',')">
                            <button value="print" x-transition x-show="cekPrint.length" class="btn btn-sm btn-info"
                                name="submit">
                                <i class="fas fa-print"></i>
                                Print
                                <span class="badge bg-white text-black" x-text="cekPrint.length + ' Box'"
                                    x-transition></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl1" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="{{ $posisi == 1 ? '6' : '5' }}">
                                        ({{ count($gradingStok) }}) Grading Stock
                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center">Partai</th>
                                    <th class="dhead">Tipe - ket</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                </tr>

                                <tr>
                                    <th class="dheadstock text-center" colspan="2">Total</th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(sumCol($gradingStok, 'pcs_awal'), 0) }}</th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(sumCol($gradingStok, 'gr_awal'), 0) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradingStok as $d)
                                    <tr>
                                        <td>{{ $d->nm_partai }}</td>
                                        <td>{{ $d->tipe . ' - ' . $d->ket }}</td>
                                        <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                                        <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl1" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="{{ $posisi == 1 ? '6' : '5' }}">
                                        ({{ count($gradingSisa) }}) Grading Sisa
                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center">Invoice</th>
                                    <th class="dhead">Tipe</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                </tr>

                                <tr>
                                    <th class="dheadstock text-center" colspan="2">Total</th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(sumCol($gradingSisa, 'pcs'), 0) }}</th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(sumCol($gradingSisa, 'gr'), 0) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradingSisa as $d)
                                    <tr>
                                        <td>{{ $d->no_invoice }}</td>
                                        <td>{{ $d->tipe }}</td>
                                        <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                                        <td class="text-end">{{ number_format($d->gr, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl3" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="{{ $posisi == 1 ? '8' : '7' }}">
                                        ({{ number_format(count($gradingSelesai), 0) }}) Grading Selesai
                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center">Urutan</th>
                                    <th class="dhead text-center">Partai</th>
                                    <th class="dhead">Box Pengiriman</th>
                                    <th class="dhead">Grade</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    <th class="dhead text-center">Serah</th>
                                    {{-- <th class="dhead text-center">Print</th> --}}

                                </tr>

                                <tr>
                                    <th></th>
                                    <th class="dheadstock text-center" colspan="3">Total</th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(sumCol($gradingSelesai, 'pcs'), 0) }}</th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(sumCol($gradingSelesai, 'gr'), 0) }}</th>
                                    <th class="dheadstock text-center"> <span class="badge bg-primary"
                                            x-show="cek.length" x-text="cek.length"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradingSelesai as $d)
                                    <tr>
                                        <td>{{ $d->urutan }}</td>
                                        <td>{{ $d->nm_partai }}</td>
                                        <td>{{ $d->box_pengiriman }}</td>
                                        <td><span class="detail text-primary pointer"
                                                data-nobox="{{ $d->box_pengiriman }}">{{ $d->grade }}</span></td>
                                        <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                                        <td class="text-end">{{ number_format(floor($d->gr), 0) }}</td>
                                        <td align="center">
                                            <input type="checkbox"
                                                @change="tambah({{ $d->box_pengiriman }},'{{ $d->grade }}', {{ $d->pcs }}, {{ $d->gr }})"
                                                value="{{ $d->box_pengiriman }}" class="pointer" x-model="cek">
                                        </td>
                                        <td align="center" class="d-none">
                                            @if ($d->sudah_print == 'T')
                                                <input type="checkbox" class="form-check"
                                                    :checked="cekPrint.includes('{{ $d->box_pengiriman }}')"
                                                    @change="
                                                    if (cekPrint.includes('{{ $d->box_pengiriman }}')) {
                                                        cekPrint = cekPrint.filter(x => x !== '{{ $d->box_pengiriman }}')
                                                    } else {
                                                        cekPrint.push('{{ $d->box_pengiriman }}')
                                                    }
                                                "
                                                    name="id_print[]" id=""
                                                    value="{{ $d->box_pengiriman }}">
                                            @else
                                                <span class="badge bg-success">Y</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- detail --}}
                <x-theme.modal title="Detail" idModal="detail" btnSave="T">
                    <div class="loading d-none">
                        <x-theme.loading />
                    </div>
                    <div id="load_detail"></div>
                </x-theme.modal>

                <form action="{{ route('gradingbj.save_formulir') }}" method="post">
                    @csrf
                    <x-theme.modal idModal="tambah" title="tambah wip" btnSave="Y">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Tgl</label>
                                    <input value="{{ date('Y-m-d') }}" type="date" name="tgl"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="">Pgws Penerima</label>
                                <select required name="id_penerima" class="form-control select2" id="">
                                    <option value="">- Pilih pgws -</option>
                                    @foreach ($users as $d)
                                        <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="dhead">No Box</th>
                                            <th class="dhead text-center">Grade</th>
                                            <th class="dhead text-end">Pcs</th>
                                            <th class="dhead text-end">Gr</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <input class="d-none" name="no_box[]" type="text" :value="cek">
                                        <template x-for="item in selectedItem">
                                            <tr>
                                                <td x-text="item.no_box"></td>
                                                <td align="center" x-text="item.grade"></td>
                                                <td align="right" x-text="item.pcs"></td>
                                                <td align="right" x-text="item.gr"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </x-theme.modal>
                </form>
            </div>
        </div>
        @section('scripts')
            <script>
                ["tbl1", "tbl2", "tbl3"].forEach((tbl, i) => pencarian(`tbl${i+1}input`, tbl));

                $('.detail').click(function(e) {
                    e.preventDefault();
                    const no_box = $(this).data('nobox')
                    $("#detail").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.detail') }}",
                        data: {
                            no_box,
                        },
                        beforeSend: function() {
                            $("#load_detail").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#load_detail").html(r);
                            loadTable('tblDetail')
                        }
                    });
                });
            </script>
            <script>
                if ({{ $posisi == 1 }}) {
                    document.body.style.zoom = "90%";
                } else {
                    document.body.style.zoom = "75%";
                }
            </script>
        @endsection
    </x-slot>

</x-theme.app>
