<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>


        </div>
        <span class="text-warning fst-italic">Sebelum di masukan barcode, cek dulu box grading nya , jika ada yang kurang
            tambahkan, jika kelebihan maka dihapus.</span>
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
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <input type="text" id="tbl3input" class=" form-control form-control-sm mb-2"
                                placeholder="cari">
                        </div>
                        <div>
                            <button data-bs-target="#tambah" data-bs-toggle="modal" class="btn btn-sm btn-primary"
                                type="button"><i class="fas fa-plus"></i> Box</button>
                            <x-theme.modal btnSave="T" title="tambah box" idModal="tambah">


                                <div class="scrollable-table col-lg-12" x-data="{ cek: [], ttlPcs: 0, ttlGr: 0 }">
                                    <form id="formTambahBox" action="{{ route('pengiriman.kirim') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="no_box" class="form-control"
                                            :value="cek.join(',')">
                                        <input type="hidden" name="no_nota" class="form-control"
                                            value="{{ $no_nota }}">
                                        <div class="d-flex gap-2">
                                            <div class="flex-grow-1">
                                                <input autocomplete="off" type="text" id="tbl1input"
                                                    class="form-control form-control-sm mb-2" placeholder="cari">
                                            </div>
                                            <div>
                                                <button x-transition x-show="cek.length" id="simpanTambahBox"
                                                    class="btn  btn-sm btn-primary" type="button">
                                                    <i class="fas fa-plus"></i>
                                                    Kirim
                                                    <span class="badge bg-info" x-text="cek.length" x-transition></span>
                                                    <span x-transition><span x-text="ttlPcs"></span> Pcs <span
                                                            x-text="ttlGr"></span> Gr</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="loadTblTmbhBox"></div>
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
                                                if (
                                                    $d->pcs - $d->pcs_pengiriman >= 0 &&
                                                    $d->gr - $d->gr_pengiriman > 0
                                                ) {
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
                                                        <td class="text-end">
                                                            {{ number_format($d->pcs - $d->pcs_pengiriman, 0) }}</td>
                                                        <td class="text-end">
                                                            {{ number_format($d->gr - $d->gr_pengiriman, 0) }}</td>

                                                        <td align="right" class="d-flex justify-content-evenly">
                                                            <input type="checkbox" class="form-check"
                                                                :checked="cek.includes('{{ $d->no_box }}')"
                                                                name="id[]" id=""
                                                                value="{{ $d->no_box }}">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </x-theme.modal>
                        </div>
                        <div>
                            <a href="{{ route('pengiriman.po_export', $no_nota) }}" class="btn btn-sm btn-primary"><i
                                    class="fas fa-file-excel"></i> Export</a>
                        </div>
                    </div>
                    <div id="loadTblPo"></div>

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
                function loadTblTmbhBox() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('pengiriman.load_tbl_loadTblTmbhBox') }}",
                        success: function(r) {
                            $("#loadTblTmbhBox").html(r);
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
                        }
                    });
                }
                function loadTbl() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('pengiriman.load_tbl_po') }}?no_nota={{ $no_nota }}",
                        success: function(r) {
                            $("#loadTblPo").html(r);
                        }
                    });
                }

                function hapus(id) {
                    if (confirm("Apakah Anda yakin?")) {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('pengiriman.delete') }}?id=" + id,
                            success: function(r) {
                                alertToast('sukses', 'Berhasil di hapus');
                                loadTbl()
                            }
                        });
                    }
                }

                function ubah(element) {
                    let row = $(element).closest('tr');

                    // Ambil nilai dari input yang baru diketik
                    let id_pengiriman = row.find('input[name="id_pengiriman[]"]').val();
                    let grade = row.find('input[name="grade2[]"]').val();
                    let pcs = row.find('input[name="pcs2[]"]').val();
                    let gr = row.find('input[name="gr2[]"]').val();
                    let barcode = row.find('input[name="barcode[]"]').val();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('pengiriman.ubah') }}",
                        data: {
                            id_pengiriman,
                            grade,
                            pcs,
                            gr,
                            barcode
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil di ubah');
                            loadTbl()
                        }
                    });
                }
                loadTbl()
                loadTblTmbhBox()

                $("#simpanTambahBox").click(function(e) {
                    e.preventDefault();
                    let noBoxValue = $('input[name="no_box"]').val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('pengiriman.kirim') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            no_box: noBoxValue,
                            no_nota: "{{ $no_nota }}"
                        },
                        success: function(r) {
                            loadTbl()
                            $("#tambah").modal('hide')
                        }
                    });
                });
                pencarian('tbl1input', 'tbl1')

                

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
