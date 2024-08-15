<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">


        </div>

    </x-slot>

    <x-slot name="cardBody">
        <table class="mb-2" border="2">
            <tr>
                <td>A=(B2/B1)+C</td>
            </tr>
            <tr>
                <td colspan="2">A=B1+C</td>
                <td>hasil</td>
                <td>sisa</td>
            </tr>
            <tr>
                <td>gr awal</td>
                <td>gr awal</td>
                <td>gr akhir</td>
                <td>gr awal</td>
            </tr>
            <tr>
                <td class="bg-warning text-white">A</td>
                <td class="bg-warning text-white">B1</td>
                <td>B2</td>
                <td class="bg-warning text-white">C</td>
                <td>susut</td>
            </tr>
        </table>
        <div class="d-flex gap-1">
            <div>
                <table class="table table-hover table-border table-striped">
                    <thead>

                        <tr>
                            <th colspan="2" class="dhead">Akhir</th>
                            <th class="dhead">A</th>
                            <th colspan="2" class="dhead">awal</th>
                            <th class="dhead">B1</th>
                            <th class="dhead" colspan="2">awal</th>
                            <th class="dhead">B2</th>
                            <th class="dhead" colspan="5">akhir</th>
                            <th class="dhead">C (sisa)</th>
                            <th class="dhead" colspan="2">awal</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="dhead">v</th>
                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>

                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>

                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>
                            <th class="dhead">cost kerja</th>
                            <th class="dhead">cost op</th>
                            <th class="dhead">cost dll,cu,denda</th>

                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>
                        </tr>
                    </thead>
                    @php
                        $index = 1;
                    @endphp
                    @foreach ($datas as $i => $d)
                        <tr >
                            <th class="bg-primary text-white" colspan="17">{{ $i }}</th>
                        </tr>
                        @php
                            $increment = 1;
                        @endphp
                        @php
                            $sumTtl = [
                                'apcs' => 0,
                                'agr' => 0,
                                'arp' => 0,
                                'b1pcs' => 0,
                                'b1gr' => 0,
                                'b1rp' => 0,
                                'b2pcs' => 0,
                                'b2gr' => 0,
                                'b2rp' => 0,
                                'cost_kerja' => 0,
                                'cost_op' => 0,
                                'cost_dll' => 0,
                                'cpcs' => 0,
                                'cgr' => 0,
                                'crp' => 0,
                            ];
                        @endphp
                        @foreach ($d as $item)
                            @foreach ($item as $key => $value)
                                @php
                                    $apcs = isset($value['apcs']) ? number_format($value['apcs']) : '';
                                    $agr = isset($value['agr']) ? number_format($value['agr']) : '';
                                    $arp = isset($value['arp']) ? number_format($value['arp']) : '';

                                    $b1pcs = isset($value['b1pcs']) ? number_format($value['b1pcs']) : '';
                                    $b1gr = isset($value['b1gr']) ? number_format($value['b1gr']) : '';
                                    $b1rp = isset($value['b1rp']) ? number_format($value['b1rp']) : '';

                                    $b2pcs = isset($value['b2pcs']) ? number_format($value['b2pcs']) : '';
                                    $b2gr = isset($value['b2gr']) ? number_format($value['b2gr']) : '';
                                    $b2rp = isset($value['b2rp']) ? number_format($value['b2rp']) : '';

                                    $cost_kerja = isset($value['cost_kerja'])
                                        ? number_format($value['cost_kerja'])
                                        : '';
                                    $cost_op = isset($value['cost_op']) ? number_format($value['cost_op']) : '';
                                    $cost_dll = isset($value['cost_dll']) ? number_format($value['cost_dll']) : '';

                                    $cpcs = isset($value['cpcs']) ? number_format($value['cpcs']) : '';
                                    $cgr = isset($value['cgr']) ? number_format($value['cgr']) : '';
                                    $crp = isset($value['crp']) ? number_format($value['crp']) : '';

                                    $keys = [
                                        'apcs',
                                        'agr',
                                        'arp',
                                        'b1pcs',
                                        'b1gr',
                                        'b1rp',
                                        'b2pcs',
                                        'b2gr',
                                        'b2rp',
                                        'cost_kerja',
                                        'cost_op',
                                        'cost_dll',
                                        'cpcs',
                                        'cgr',
                                        'crp',
                                    ];

                                    foreach ($keys as $isi) {
                                        if (isset($value[$isi])) {
                                            $sumTtl[$isi] += $value[$isi];
                                        }
                                    }
                                @endphp

                                <tr class="pointer detail" index="{{ "$index$increment" }}">
                                    <td>{{ preg_replace('/[0-9]+/', '', $key) }}</td>
                                    <td>{{ $value['label'] }}</td>

                                    <td>{{ $apcs }}</td>
                                    <td>{{ $agr }}</td>
                                    <td>{{ $arp }}</td>

                                    <td>{{ $b1pcs }}</td>
                                    <td>{{ $b1gr }}</td>
                                    <td>{{ $b1rp }}</td>

                                    <td>{{ $b2pcs }}</td>
                                    <td>{{ $b2gr }}</td>
                                    <td>{{ $b2rp }}</td>

                                    <td>{{ $cost_kerja }}</td>
                                    <td>{{ $cost_op }}</td>
                                    <td>{{ $cost_dll }}</td>

                                    <td>{{ $cpcs }}</td>
                                    <td>{{ $cgr }}</td>
                                    <td>{{ $crp }}</td>
                                </tr>
                                @php
                                    $increment++;
                                @endphp
                            @endforeach
                        @endforeach
                        <tr>
                            <td></td>
                            <th>Total</th>
                            @foreach ($sumTtl as $i => $d)
                                <th>
                                    @php
                                        // Menghitung nilai yang sesuai untuk kolom saat ini
                                        $value = $d;

                                        if ($i == 'b1pcs') {
                                            $value = ($sumTtl['apcs'] ?? 0) - ($sumTtl['cpcs'] ?? 0);
                                        }

                                        if ($i == 'b1gr') {
                                            $value = ($sumTtl['agr'] ?? 0) - ($sumTtl['cgr'] ?? 0);
                                        }

                                        // Calculate b1rp as arp - crp
                                        if ($i == 'b1rp') {
                                            $value = ($sumTtl['arp'] ?? 0) - ($sumTtl['crp'] ?? 0);
                                        }

                                        // Mengatur nilai untuk cost_dll dan cost_op
                                        if ($i == 'cost_dll' || $i == 'cost_op') {
                                            $value = 0;
                                        }
                                    @endphp

                                    {{ number_format($value, 0) }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @php
                                $b1gr = $sumTtl['agr'] - $sumTtl['cgr'];
                                $susut =  (1 - ($sumTtl['b2gr'] / $b1gr)) * 100
                            @endphp
                            <td></td>
                            <th>Susut</th>
                            @for ($i = 0; $i <= 14; $i++)
                                <th>{{ $i == 7 ? number_format($susut,0). '%' : '' }}</th>
                            @endfor
                        </tr>

                        @php
                            $index++;
                        @endphp
                    @endforeach
                </table>
            </div>

        </div>


        <x-theme.modal btnSave="T" size="modal-lg" idModal="detail" title="Detail Summary">
            <div class="loading d-none">
                <x-theme.loading />
            </div>
            <div id="loadDetail"></div>
        </x-theme.modal>

    </x-slot>
    @section('scripts')
        <script>
            $('.detail').click(function(e) {
                e.preventDefault();
                const index = $(this).attr('index')
                $('#detail').modal('show')
                $.ajax({
                    type: "GET",
                    url: "{{ route('gudang.detailSummaryIbu') }}?index=" + index,
                    beforeSend: function() {
                        $("#loadDetail").html("");
                        $('.loading').removeClass('d-none');
                    },
                    success: function(r) {
                        $('.loading').addClass('d-none');
                        $("#loadDetail").html(r);
                        loadTable('tblSummary')
                    }
                });
            });
        </script>
    @endsection
</x-theme.app>
