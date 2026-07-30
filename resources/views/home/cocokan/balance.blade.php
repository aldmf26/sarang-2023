<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        @section('styles')
            <style>
                .balance-table-scroll {
                    max-height: 500px;
                    overflow-y: auto;
                }

                .bg-primary2 {
                    background-color: #f7bac5 !important;
                }

                .bg-warning2 {
                    background-color: #f7f700 !important;
                }
            </style>
        @endsection

        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-5">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Bk Kerja</h6>
                    <input autofocus placeholder="Pencarian" type="text" id="tbl1input"
                        class="form-control form-control-sm w-auto">
                </div>

                <div class="balance-table-scroll">
                    <table class="table table-bordered" id="tbl1">
                        <thead>
                            <tr>
                                <th class="dhead">No</th>
                                <th class="dhead">Bulan kerja</th>
                                <th class="dhead">Nama partai</th>
                                <th class="dhead">Keterangan</th>
                                <th class="dhead">Grade</th>
                                <th class="text-end dhead">Pcs</th>
                                <th class="text-end dhead">Gr</th>
                                <th class="text-end dhead">Ttl Rp</th>
                                <th class="text-end dhead">Rata2</th>
                            </tr>
                            <tr>
                                <th class="dhead"></th>
                                <th class="dhead">Total</th>
                                <th class="dhead"></th>
                                <th class="dhead"></th>
                                <th class="dhead"></th>
                                <th class="text-end dhead">
                                    {{ number_format($bk_totals['pcs'], 0) }}
                                </th>
                                <th class="text-end dhead">
                                    {{ number_format($bk_totals['gr'], 0) }}
                                </th>
                                <th class="text-end dhead">
                                    {{ number_format($bk_totals['total'], 0) }}
                                </th>
                                <th class="text-end dhead">
                                    {{ number_format($bk_totals['average'], 0) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bk as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ empty($row->bulan) ? '-' : date('F Y', strtotime("{$row->tahun}-{$row->bulan}-01")) }}
                                    </td>
                                    <td>{{ $row->nm_partai }}</td>
                                    <td>{{ $row->nm_partai_dulu }}</td>
                                    <td>{{ $row->grade }}</td>
                                    <td class="text-end">{{ number_format($row->pcs_bk, 0) }}</td>
                                    <td class="text-end">{{ number_format($row->gr_bk, 0) }}</td>
                                    <td class="text-end">{{ number_format($row->cost_bk, 0) }}</td>
                                    <td class="text-end">
                                        {{ $row->gr_bk > 0 ? number_format($row->cost_bk / $row->gr_bk, 0) : 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-3">
                <h6>Cost Perbulan</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Bulan & tahun</th>
                            <th class="text-end dhead">Gaji</th>
                            <th class="text-end dhead">Cost operasional</th>
                            <th class="text-end dhead">Total Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cost_rows as $cost)
                            <tr>
                                <td>{{ $cost['periode'] }}</td>
                                <td class="text-end">
                                    <a target="_blank"
                                        href="{{ route('cocokan.balance.gaji', ['bulan' => $cost['bulan'], 'tahun' => $cost['tahun']]) }}">
                                        {{ number_format($cost['gaji'], 0) }}
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a target="_blank"
                                        href="{{ route('cocokan.balance.cost', ['bulan' => $cost['bulan'], 'tahun' => $cost['tahun']]) }}">
                                        {{ number_format($cost['operasional'], 0) }}
                                    </a>
                                </td>
                                <td class="text-end">{{ number_format($cost['total'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-end">{{ number_format($cost_totals['gaji'], 0) }}</th>
                            <th class="text-end">{{ number_format($cost_totals['operasional'], 0) }}</th>
                            <th class="text-end">{{ number_format($cost_totals['total'], 0) }}</th>
                        </tr>
                        <tr>
                            <th>Cost Berjalan</th>
                            <th colspan="2"></th>
                            <th class="text-end">{{ number_format($cost_berjalan, 0) }}</th>
                        </tr>
                        <tr>
                            <th class="dhead">Total Bk + Operasional + cost berjalan</th>
                            <th class="dhead" colspan="2"></th>
                            <th class="text-end dhead">{{ number_format($balance_totals['total'], 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-lg-4">
                <div class="d-flex justify-content-between">
                    <h6>Bk Rp</h6>
                    @include('home.cocokan.btn_export', ['divisi' => 'balance'])
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Ket</th>
                            <th class="text-end dhead">Pcs</th>
                            <th class="text-end dhead">Gr</th>
                            <th class="text-end dhead">Total Rp</th>
                            <th class="text-end dhead">Rata2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($balance_rows as $row)
                            <tr>
                                <td class="{{ $row['type'] === 'stock' ? 'bg-warning2' : 'bg-primary2 text-white' }}">
                                    {{ $row['label'] }}
                                </td>
                                <td class="text-end">{{ number_format($row['pcs'], 0) }}</td>
                                <td class="text-end fw-bold">
                                    @if ($row['detail_id'] > 0)
                                        <a href="#" class="detailbalance" data-row="{{ $row['detail_id'] }}"
                                            data-bs-toggle="modal" data-bs-target="#detailBalance">
                                            {{ number_format($row['gr'], 0) }}
                                        </a>
                                    @else
                                        {{ number_format($row['gr'], 0) }}
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($row['total'], 0) }}</td>
                                <td class="text-end">{{ number_format($row['average'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dhead">Total</th>
                            <th class="dhead text-end">{{ number_format($balance_totals['pcs'], 0) }}</th>
                            <th class="dhead text-end">{{ number_format($balance_totals['gr'], 0) }}</th>
                            <th class="dhead text-end">{{ number_format($balance_totals['total'], 0) }}</th>
                            <th class="dhead text-end">
                                {{ $balance_totals['gr'] > 0 ? number_format($balance_totals['total'] / $balance_totals['gr'], 0) : 0 }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="modal fade" id="detailBalance" tabindex="-1" aria-labelledby="detailBalanceLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailBalanceLabel">Detail Balance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_detail"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1');

                function loadOperationalCost(period = null) {
                    $.get("{{ route('summary.get_operasional') }}", {
                        id_oprasional: period
                    }, function(response) {
                        $('#cost_opr').html(response);
                    });
                }

                loadOperationalCost();

                const detailRoutes = @json($detail_routes);

                $(document).on('change', '.bulan_op', function() {
                    loadOperationalCost($(this).val());
                });

                $(document).on('click', '.detailbalance', function(event) {
                    event.preventDefault();

                    const url = detailRoutes[$(this).data('row')];
                    if (!url) {
                        return;
                    }

                    $.get(url, function(response) {
                        $('#load_detail').html(response);
                        $('#tableHalaman').DataTable({
                            searching: true,
                            autoWidth: true,
                            paging: true,
                            ordering: true
                        });
                    });
                });

                function numberFormat(initialValue) {
                    return {
                        formattedNumber: new Intl.NumberFormat().format(initialValue),
                        formatNumber() {
                            const rawNumber = this.formattedNumber.replace(/\D/g, '');
                            this.formattedNumber = new Intl.NumberFormat().format(rawNumber);
                        }
                    };
                }
            </script>
        @endsection
    </x-slot>
</x-theme.app>
