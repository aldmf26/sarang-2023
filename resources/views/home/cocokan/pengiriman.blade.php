<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <button type="button" class="btn btn-sm btn-success float-end" data-bs-toggle="modal" data-bs-target="#exportDetailPengirimanModal">
            <i class="fas fa-file-excel me-1"></i> Export detail pengiriman
        </button>
    </x-slot>

    <x-slot name="cardBody">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <p class="mb-1">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="modal fade" id="exportDetailPengirimanModal" tabindex="-1" aria-labelledby="exportDetailPengirimanLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="GET" action="{{ route('cocokan.pengiriman.export_detail') }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exportDetailPengirimanLabel">Pilih nomor packing list</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                            <p class="text-muted">Pilih packing list untuk melihat detail setiap grade dan box pengiriman.</p>
                            <input type="search" class="form-control mb-3" id="packingListSearch"
                                placeholder="Cari nomor PI, misalnya 1012" autocomplete="off">
                            <div id="packingListOptions">
                            @forelse ($packingLists as $packingList)
                                <div class="border rounded p-2 mb-2 packing-list-option"
                                    data-search="{{ strtolower($packingList->no_nota . ' ' . ($packingList->nm_packing ?: '') . ' ' . ($packingList->tujuan ?: '')) }}">
                                    <div class="form-check">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="no_nota[]" value="{{ $packingList->no_nota }}" id="packing_{{ $packingList->no_nota }}">
                                    <label class="form-check-label" for="packing_{{ $packingList->no_nota }}">
                                        <strong>PI {{ $packingList->no_nota }}</strong> — {{ $packingList->nm_packing ?: 'Tanpa nama' }}
                                        <span class="text-muted">| Tujuan: {{ $packingList->tujuan ?: '-' }} | {{ $packingList->jumlah_box }} box</span>
                                    </label>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info mb-0">Belum ada packing list.</div>
                            @endforelse
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-download me-1"></i> Export detail</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const search = document.getElementById('packingListSearch');
                if (!search) return;
                search.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    document.querySelectorAll('#packingListOptions .packing-list-option').forEach(function (item) {
                        item.classList.toggle('d-none', query !== '' && !item.dataset.search.includes(query));
                    });
                });
            });
        </script>
        <section class="row">
            @include('home.cocokan.nav')
            <div class="col-lg-3">
                <h6>{{ $title }}</h6>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                    </tr>
                    <tr>
                        <td>Wip2 akhir</td>
                        <td class="text-end">{{ number_format($wip2akhir->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($wip2akhir->gr, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($wip2akhir->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($wip2akhir->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($wip2akhir->gr, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($wip2akhir->ttl_rp, 0) }}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-4">
                <h6>Pengiriman Kerja</h6>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                        <th class="dhead text-end">Cost kerja</th>
                        <th class="dhead text-end">Total Rp</th>
                        <th class="dhead text-end">Rata2</th>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Pengiriman</td>
                        <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.pengiriman.awal') }}" target="_blank">
                                {{ number_format($pengiriman->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($pengiriman->cost_bk + $pengiriman->cost_op + $pengiriman->cost_kerja + $pengiriman->cost_cu, 0) }}
                        </td>

                        <td class="text-end">0</td>
                        <td class="text-end">
                            {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($pengiriman->gr > 0 ? ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op) / $pengiriman->gr : 0, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Pengiriman proses</td>
                        <td class="text-end">{{ number_format($pengiriman_proses->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.pengiriman.sisa') }}" target="_blank">
                                {{ number_format($pengiriman_proses->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($pengiriman_proses->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">0</td>
                        <td class="text-end">
                            {{ number_format($pengiriman_proses->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">
                            {{ empty($pengiriman_proses->gr) ? 0 : number_format($pengiriman_proses->ttl_rp / $pengiriman_proses->gr, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($pengiriman->pcs + $pengiriman_proses->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($pengiriman->gr + $pengiriman_proses->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($pengiriman_proses->ttl_rp + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op), 0) }}
                        </td>
                        <td></td>
                        <td class="text-end fw-bold">
                            {{ number_format($pengiriman_proses->ttl_rp + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op), 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($pengiriman->gr + $pengiriman_proses->gr) > 0 ? ($pengiriman_proses->ttl_rp + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op)) / ($pengiriman->gr + $pengiriman_proses->gr) : 0, 0) }}
                        </td>
                    </tr>



                </table>
            </div>
            <div class="col-lg-5">
                <div class="d-flex justify-content-between">
                    <h6>List Pengiriman</h6>
                    <div>
                        @include('home.cocokan.btn_export', ['divisi' => 'pengiriman'])
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Tgl Kirim</th>
                            <th class="dhead">No Packinglist</th>
                            <th class="dhead">Nama Packing List</th>
                            <th class="dhead">Tujuan</th>
                            <th class="dhead text-end">Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Gr + Kadar</th>
                            <th class="dhead text-end">Total Rp</th>
                            <th class="dhead text-end">Rata2</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($list_pengiriman as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="white-space: nowrap">{{ tanggal($d->tgl) }}</td>
                                <td>PI {{ $d->no_nota }}</td>
                                <td>{{ ucwords($d->nm_packing) }}</td>
                                <td>{{ strtoupper($d->tujuan) }}</td>
                                <td align="center">{{ $d->ttl_box }}</td>
                                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                <td align="right"><a target="_blank"
                                        href="{{ route('detail.list_pengiriman', ['no_nota' => $d->no_nota]) }}">{{ number_format($d->gr, 0) }}</a>
                                </td>
                                <td align="right">{{ number_format($d->gr_naik, 0) }}</td>
                                <td align="right">
                                    {{ number_format($d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu, 0) }}
                                </td>
                                <td align="right">
                                    {{ number_format($d->gr > 0 ? ($d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu) / $d->gr : 0, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="fw-bold">Total</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman, 'pcs'), 0) }}</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman, 'gr'), 0) }}</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman, 'gr_naik'), 0) }}
                            </th>
                            <th class="text-end fw-bold">
                                {{ number_format(sumBK($list_pengiriman, 'cost_bk') + sumBk($list_pengiriman, 'cost_op') + sumBK($list_pengiriman, 'cost_kerja') + sumBK($list_pengiriman, 'cost_cu'), 0) }}
                            </th>
                            <th class="text-end fw-bold">
                                {{ number_format(empty(sumBK($list_pengiriman, 'gr')) ? 0 : (sumBK($list_pengiriman, 'cost_bk') + sumBk($list_pengiriman, 'cost_op') + sumBK($list_pengiriman, 'cost_kerja') + sumBK($list_pengiriman, 'cost_cu')) / sumBK($list_pengiriman, 'gr'), 0) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4"></div>
            <div class="col-lg-5">
                <div class="d-flex justify-content-between">
                    <h6>List Pengiriman Belum Cost</h6>

                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Tgl Kirim</th>
                            <th class="dhead">No Packinglist</th>
                            <th class="dhead">Nama Packing List</th>
                            <th class="dhead">Tujuan</th>
                            <th class="dhead text-end">Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Gr + Kadar</th>
                            <th class="dhead text-end">Total Rp</th>
                            <th class="dhead text-end">Rata2</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($list_pengiriman_belum as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="white-space: nowrap">{{ tanggal($d->tgl) }}</td>
                                <td>PI {{ $d->no_nota }}</td>
                                <td>{{ ucwords($d->nm_packing) }}</td>
                                <td>{{ strtoupper($d->tujuan) }}</td>
                                <td align="center">{{ $d->ttl_box }}</td>
                                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                <td align="right"><a target="_blank"
                                        href="{{ route('detail.list_pengiriman', ['no_nota' => $d->no_nota]) }}">{{ number_format($d->gr, 0) }}</a>
                                </td>
                                <td align="right">{{ number_format($d->gr_naik, 0) }}</td>
                                <td align="right">
                                    {{ number_format($d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu, 0) }}
                                </td>
                                <td align="right">
                                    {{ number_format($d->gr > 0 ? ($d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu) / $d->gr : 0, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="fw-bold">Total</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman_belum, 'pcs'), 0) }}
                            </th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman_belum, 'gr'), 0) }}
                            </th>
                            <th class="text-end fw-bold">
                                {{ number_format(sumBK($list_pengiriman_belum, 'gr_naik'), 0) }}
                            </th>
                            <th class="text-end fw-bold">
                                {{ number_format(sumBK($list_pengiriman_belum, 'cost_bk') + sumBk($list_pengiriman_belum, 'cost_op') + sumBK($list_pengiriman_belum, 'cost_kerja') + sumBK($list_pengiriman_belum, 'cost_cu'), 0) }}
                            </th>
                            <th class="text-end fw-bold">
                                {{ number_format(sumBK($list_pengiriman_belum, 'gr') > 0 ? (sumBK($list_pengiriman_belum, 'cost_bk') + sumBk($list_pengiriman_belum, 'cost_op') + sumBK($list_pengiriman_belum, 'cost_kerja') + sumBK($list_pengiriman_belum, 'cost_cu')) / sumBK($list_pengiriman_belum, 'gr') : 0, 0) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>








        @section('scripts')
            <script>
                get_opr();

                function get_opr() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('summary.get_operasional') }}",
                        success: function(response) {
                            $('#cost_opr').html(response);
                        }
                    });
                }
                $(document).ready(function() {
                    $(document).on("change", ".bulan_op", function(e) {
                        e.preventDefault();
                        var id_oprasional = $(this).val();


                        $.ajax({
                            type: "get",
                            url: "{{ route('summary.get_operasional') }}",
                            data: {
                                id_oprasional: id_oprasional
                            },
                            success: function(response) {
                                $('#cost_opr').html(response);
                            }
                        });

                    });
                });
            </script>
            <script>
                function numberFormat(initialValue) {
                    return {
                        formattedNumber: new Intl.NumberFormat().format(initialValue),
                        formatNumber() {
                            // Hapus karakter non-digit dan simpan nomor mentah
                            let rawNumber = this.formattedNumber.replace(/\D/g, '');

                            // Format nomor dengan pemisah ribuan
                            this.formattedNumber = new Intl.NumberFormat().format(rawNumber);
                        }
                    };
                }
            </script>
        @endsection
    </x-slot>
</x-theme.app>
