<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <button data-bs-target="#tambah" data-bs-toggle="modal" class="btn btn-sm btn-primary" type="button">Tambah Box</button>
            <x-theme.modal btnSave="T" title="tambah box" idModal="tambah">
                <input autocomplete="off" type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                
                <div class="scrollable-table col-lg-12" x-data="{ cek: [], ttlPcs: 0, ttlGr: 0 }">
                    <form action="{{ route('pengiriman.kirim') }}" method="post">
                        @csrf
                        <input type="hidden" name="no_box" class="form-control" :value="cek.join(',')">
                        <input type="hidden" name="no_nota" class="form-control" value="{{ $no_nota }}">
                        <button x-transition x-show="cek.length" class="btn btn-sm btn-primary" type="submit">
                            <i class="fas fa-plus"></i>
                            Kirim
                            <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            <span x-transition><span x-text="ttlPcs"></span> Pcs <span x-text="ttlGr"></span> Gr</span>
                        </button>
                    </form>

                    <table id="tbl1" class="mt-2 table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No Box Grading</th>
                                <th class="dhead text-center">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                {{-- <th class="dhead text-center">Detail</th> --}}
                                <th class="dhead text-center">Aksi</th>
                            </tr>

                        </thead>

                        @php
                            $ttlPcs = 0;
                            $ttlGr = 0;
                            foreach ($gudang as $d) {
                                if ($d->pcs - $d->pcs_pengiriman >= 0 && $d->gr - $d->gr_pengiriman > 0) {
                                    $ttlPcs += $d->pcs - $d->pcs_pengiriman;
                                    $ttlGr += $d->gr - $d->gr_pengiriman;
                                }
                            }
                        @endphp
                        <tr>
                            <td class=" dheadstock h6">Total</td>
                            <td class="dheadstock"></td>
                            <td class="text-end dheadstock h6 ">{{ number_format($ttlPcs, 0) }}</td>
                            <td class="text-end dheadstock h6 ">{{ number_format($ttlGr, 0) }}</td>
                           
                            <td class="dheadstock"></td>
                        </tr>
                        <tbody>
                            @foreach ($gudang as $d)
                                @if ($d->pcs - $d->pcs_pengiriman >= 0 && $d->gr - $d->gr_pengiriman > 0)
                                    <tr
                                        @click="
                                            if (cek.includes('{{ $d->no_box }}')) {
                                                cek = cek.filter(x => x !== '{{ $d->no_box }}')
                                                ttlPcs -= {{ $d->pcs - $d->pcs_pengiriman }}
                                                ttlGr -= {{ $d->gr - $d->gr_pengiriman }}
                                            } else {
                                                cek.push('{{ $d->no_box }}')
                                                ttlPcs += {{ $d->pcs - $d->pcs_pengiriman }}
                                                ttlGr += {{ $d->gr - $d->gr_pengiriman }}
                                            }
                                        ">
                                        <td>P{{ $d->no_box }}</td>
                                        <td class="text-primary text-center pointer">
                                            <span class="detail"
                                                data-nobox="{{ $d->no_box }}">{{ $d->grade }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($d->pcs - $d->pcs_pengiriman, 0) }}</td>
                                        <td class="text-end">{{ number_format($d->gr - $d->gr_pengiriman, 0) }}</td>
                                        {{-- <td class="text-center"><a
                                                href="{{ route('gradingbj.detail_pengiriman', ['no_invoice' => $d->no_invoice]) }}"
                                                target="_blank" class="badge bg-primary"><i class=" fas fa-eye"></i></a>
                                        </td> --}}
                                        <td align="right" class="d-flex justify-content-evenly">
                                            <input type="checkbox" class="form-check"
                                                :checked="cek.includes('{{ $d->no_box }}')" name="id[]"
                                                id="" value="{{ $d->no_box }}">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-theme.modal>

        </div>
        <span class="text-warning fst-italic">Sebelum di masukan barcode, cek dulu box grading nya , jika ada yang kurang tambahkan, jika kelebihan maka dihapus.</span>
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('pengiriman.save_po') }}" method="post">
            @csrf
            <div class="row" x-data="{
                kadar: 0
            }">
                <div class="col-lg-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Tgl</th>
                                <th width="120" class="dhead">No Packinglist</th>
                                <th class="dhead">Nama Packinglist</th>
                                <th class="dhead">Tujuan Ke</th>
                                <th class="dhead text-center" width="100">Kadar Air %</th>
                                <th class="dhead text-center">Ttl Box</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input class="form-control" type="date" name="tgl"
                                        value="{{ $po->tanggal }}">
                                </td>
                                <td>
                                    <input class="form-control" readonly type="text" value="PI {{ $po->no_nota }}">
                                    <input class="form-control" type="hidden" name="no_nota"
                                        value="{{ $po->no_nota }}">
                                </td>
                                <td><input type="text" placeholder="nama packinglist" name="nm_packing"
                                        class="form-control"></td>
                                <td><input type="text" placeholder="tujuan cth: hk" name="tujuan"
                                        class="form-control"></td>
                                <td><input type="text" x-model="kadar" placeholder="kadar %" name="kadar"
                                        class="form-control text-end">
                                </td>
                                <td align="center">
                                    <h6>{{ number_format($po->ttl, 0) }}</h6>
                                </td>
                                <td>
                                    <h6>{{ number_format($po->pcs, 0) }}</h6>
                                </td>
                                <td>
                                    <h6>{{ number_format($po->gr, 0) }}</h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12">
                    <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <table class="table table-bordered" id="tbl3">
                        <thead>
                            <tr>
                                <th class="dhead">No</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Grade 1</th>
                                <th class="dhead text-end">Pcs 1</th>
                                <th class="dhead text-end">Gr 1</th>

                                <th width="120" class="bg-info text-white text-center">Grade 2</th>
                                <th width="120" class="bg-info text-white text-end">Pcs 2</th>
                                <th width="120" class="bg-info text-white text-end">Gr 2</th>
                                <th width="120" class="bg-info text-white">No Barcode Pengiriman</th>

                                <th class="dhead text-end">Pcs Kirim</th>
                                <th class="dhead text-end">Gr Kirim air %</th>
                                <th class="dhead text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengiriman as $i => $d)
                                <tr x-data="{
                                    gr2: {{ $d->gr }},
                                }">
                                    <td>{{ $i + 1 }} </td>
                                    <td>P{{ $d->no_box }}</td>
                                    <td align="center">{{ $d->grade }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>

                                    <td align="center">
                                        <input onclick="$(this).select()" name="grade2[]" required value="{{ $d->grade }}" type="text"
                                            class="form-control">
                                        <input name="box_grading[]" required value="{{ $d->no_box }}" type="hidden"
                                            class="form-control">
                                    </td>
                                    <td align="center">
                                        <input onclick="$(this).select()" name="pcs2[]" required value="{{ $d->pcs }}" type="text"
                                            class="text-end form-control">
                                    </td>
                                    <td align="center">
                                        <input name="id_pengiriman[]" required value="{{ $d->id_pengiriman }}"
                                            type="hidden">
                                        <input onclick="$(this).select()" name="gr2[]" x-model="gr2" required value="{{ $d->gr }}"
                                            type="text" class="text-end form-control">
                                    </td>
                                    <td align="center">
                                        <input  onclick="$(this).select()" name="barcode[]" value="{{ $d->no_barcode }}"
                                            placeholder="cth: 10001" autocomplete="off" type="text" class="form-control">
                                    </td>

                                    <td align="right">
                                        {{ $d->pcs }}
                                    </td>
                                    <td align="right" x-text="(Number(gr2) / Number(kadar)) + Number(gr2)">-
                                    </td>
                                    <td align="center"><a onclick="return confirm('yakin di hapus ?')" href="{{route('pengiriman.delete', ['id' => $d->id_pengiriman])}}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex gap-2 float-end">
                <button name="submit" value="draft" class="btn btn-info btn-sm">
                    Kembali / Draft
                </button>
                <button class="btn btn-primary btn-sm" value="simpan" name="submit">Save</button>
            </div>

            </section>
        </form>

        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')

                $('#tbl3').on('keydown', 'input[type="text"]', function(e) {
                    const $currentCell = $(this).closest('td');
                    const columnIndex = $currentCell.index();
                    const $currentRow = $currentCell.parent();

                    switch (e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            const $nextRow = $currentRow.next('tr');
                            if ($nextRow.length) {
                                $nextRow.find(`td:eq(${columnIndex}) input[type="text"]`).focus();
                            }
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            const $prevRow = $currentRow.prev('tr');
                            if ($prevRow.length) {
                                $prevRow.find(`td:eq(${columnIndex}) input[type="text"]`).focus();
                            }
                            break;
                    }
                });
                pencarian('tbl3input', 'tbl3')
                $(document).on('click', '.edit', function(e) {
                    e.preventDefault();

                    var no_invoice = $(this).data('no_invoice');
                    var kategori = $(this).data('kategori');

                    $("#edit").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gudangsarang.load_edit_invoice') }}",
                        data: {
                            no_invoice,
                            kategori,
                        },
                        beforeSend: function() {
                            $("#load_edit").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#load_edit").html(r);
                            pencarian('inputTbl', 'tbl1')
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
