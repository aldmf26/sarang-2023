<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <div class="row">
            @foreach ($cabut as $i => $d)
                @php
                    $title = $d['title'];
                    $query = $d['query'] ?? [];

                    // $pcs = $d['body']['pcs'] ?? 0;
                    // $gr = $d['body']['gr'] ?? 0;
                    // $ttl_rp = $d['body']['ttl_rp'] ?? 0;

                @endphp
                <div class="col-md-4">
                    <div no="{{ $i + 1 }}"
                        class="detail position-relative card cardHover pointer text-center border border-secondary">
                        <div class="card-body">
                            <h5>{{ $i + 1 }}</h5>
                            <h6>{{ $title }}</h6>
                            <table class="table text-end">
                                <thead>
                                    <tr>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead">Pcs</th>
                                        <th class="dhead">Gr</th>
                                        <th class="dhead">Rp/gr</th>
                                        <th class="dhead">Ttl Rp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ttlPcs = 0;
                                        $ttlGr = 0;
                                        $ttlTtlRp = 0;
                                    @endphp
                                    @foreach ($query as $q)
                                        @php
                                            switch ($i) {
                                                case 3:
                                                    $pcs = $q->pcs_awal;
                                                    $gr = $q->gr_awal + $q->gr_eoeo;

                                                    break;
                                                case 4:
                                                    $pcs = $q->pcs_awal - $q->pcs_akhir;
                                                    $gr = ($q->gr_awal + $q->gr_eoeo) - ($q->gr_akhir + $q->gr_eoeo_akhir);
                                                    break;
                                                case 5:
                                                    $pcs = $q->pcs_akhir;
                                                    $gr = ($q->gr_akhir + $q->gr_eoeo_akhir);
                                                    $ttlRp = $q->ttl_rp + $q->eo_ttl_rp;

                                                    break;

                                                default:
                                                    # code...
                                                    break;
                                            }
                                            $ttlPcs += $pcs;
                                                    $ttlGr += $gr;
                                                    $ttlTtlRp += $ttlRp ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="text-start">{{ $q->tipe }}</td>
                                            <td align="right">{{ number_format($pcs, 0) }}</td>
                                            <td align="right">{{ number_format($gr, 0) }}</td>
                                            <td align="right">{{ number_format(empty($ttlRp) ? 0 : $ttlRp / $gr, 0) }}</td>
                                            <td align="right">{{ number_format($ttlRp ?? 0, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="dhead">TOTAL</th>
                                        <th class="dhead">{{ number_format($ttlPcs, 0) }}</th>
                                        <th class="dhead">{{ number_format($ttlGr, 0) }}</th>
                                        <th class="dhead">{{ number_format(empty($ttlGr) ? 0 : $ttlTtlRp / $ttlGr, 0) }}</th>
                                        <th class="dhead">{{ number_format($ttlTtlRp, 0) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @if (str_contains(strtolower($d['title']), 'sisa'))
                            <div class="position-absolute top-50 start-50 translate-middle p-2 bg-transparent">
                                <h6 style="transform: rotate(-15deg);" class="text-muted opacity-50 mb-0"
                                    style="font-style: italic;">Diopname</h6>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-4 bg-light">
                <div class="card sticky-top cardHover pointer bg-info">
                    <div class="card-body">
                        <h5 class="text-white ">Cabut</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">

                    @foreach ($cabut as $i => $d)
                        @php
                            $title = $d['title'];
                            $query = $d['query'] ?? [];

                            // $pcs = $d['body']['pcs'] ?? 0;
                            // $gr = $d['body']['gr'] ?? 0;
                            // $ttl_rp = $d['body']['ttl_rp'] ?? 0;

                        @endphp
                        <div class="col-md-12">
                            <div no="{{ $i + 1 }}"
                                class=" position-relative card cardHover pointer text-center border border-secondary">
                                <div class="card-body">
                                    <h5>{{ $i + 1 }}</h5>
                                    <h6>{{ $title }}</h6>
                                    <table class="table text-end">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Pcs</th>
                                                <th>Gr</th>
                                                <th>Rp/gr</th>
                                                <th>Ttl Rp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($query as $q)
                                                <tr>
                                                    <th class="text-start">{{ $q->tipe }}</th>
                                                    <th>{{ number_format($q->pcs_awal, 0) }}</th>
                                                    <th>{{ number_format($q->gr_awal + $q->gr_eoeo, 0) }}</th>
                                                    <th>{{ number_format(9999, 0) }}</th>
                                                    <th>{{ number_format(9999, 0) }}</th>
                                                    <th>{{ number_format(9999, 0) }}</th>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (str_contains(strtolower($d['title']), 'sisa'))
                                    <div class="position-absolute top-50 start-50 translate-middle p-2 bg-transparent">
                                        <h6 style="transform: rotate(-15deg);" class="text-muted mb-0"
                                            style="font-style: italic;">Diopname</h6>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        <hr>
        {{-- <div class="row">
            <div class="col-md-4 bg-light">
                <div class="card sticky-top cardHover pointer bg-info">
                    <div class="card-body">
                        <h5 class="text-white ">Cetak</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    @foreach ($cards as $i => $d)
                        @if ($i < 6)
                            @continue
                        @endif
                        @php
                            $pcs = $d['body']['pcs'] ?? 0;
                            $gr = $d['body']['gr'] ?? 0;
                            $ttl_rp = $d['body']['ttl_rp'] ?? 0;
                        @endphp
                        <div class="col-md-4">
                            <div no="{{ $i }}"
                                class="position-relative card cardHover pointer text-center border border-secondary">
                                <div class="card-body">
                                    <h5>{{ $i }}</h5>
                                    <h6>{{ strtoupper($d['title']) }}</h6>
                                    <table class="table text-end">
                                        <tr>
                                            <th class="text-start">Pcs</th>
                                            <th>:</th>
                                            <th>{{ number_format($pcs, 0) }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-start">Gr</th>
                                            <th>:</th>
                                            <th>{{ number_format($gr, 0) }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-start">Ttl Rp</th>
                                            <th>:</th>
                                            <th>{{ number_format($ttl_rp, 0) }}</th>
                                        </tr>
                                    </table>
                                </div>
                                @if (str_contains(strtolower($d['title']), 'sisa'))
                                    <div class="position-absolute top-50 start-50 translate-middle p-2 bg-transparent">
                                        <h6 style="transform: rotate(-15deg);" class="text-muted mb-0"
                                            style="font-style: italic;">Diopname</h6>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4 bg-light">
                <div class="card sticky-top cardHover pointer bg-info">
                    <div class="card-body">
                        <h5 class="text-white ">Grading</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    @foreach ($cards as $i => $d)
                        @if (9 < $i && $i <= 13)
                            @php
                                $pcs = $d['body']['pcs'] ?? 0;
                                $gr = $d['body']['gr'] ?? 0;
                                $ttl_rp = $d['body']['ttl_rp'] ?? 0;
                            @endphp
                            <div class="col-md-4">
                                <div no="{{ $i + 1 }}"
                                    class=" card cardHover pointer text-center border border-secondary">
                                    <div class="card-body">
                                        <h5>{{ $i + 1 }}</h5>
                                        <h6>{{ strtoupper($d['title']) }}</h6>
                                        <table class="table text-end">
                                            <tr>
                                                <th class="text-start">Pcs</th>
                                                <th>:</th>
                                                <th>{{ number_format($pcs, 0) }}</th>
                                            </tr>
                                            <tr>
                                                <th class="text-start">Gr</th>
                                                <th>:</th>
                                                <th>{{ number_format($gr, 0) }}</th>
                                            </tr>
                                            <tr>
                                                <th class="text-start">Ttl Rp</th>
                                                <th>:</th>
                                                <th>{{ number_format($ttl_rp, 0) }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div> --}}
        {{-- <h6 class="bg-primary p-2 text-white">CABUT</h6>
        <div class="row">
            @foreach ($cards as $i => $d)
                @php
                    $pcs = $d['body']['pcs'] ?? 0;
                    $gr = $d['body']['gr'] ?? 0;
                    $ttl_rp = $d['body']['ttl_rp'] ?? 0;
                @endphp
                <div class="col-lg-3">
                    <div no="{{ $i + 1 }}"
                        class=" card cardHover pointer text-center border border-secondary">
                        <div class="card-body">
                            <h5>{{ $i + 1 }}</h5>
                            <h6>{{ strtoupper($d['title']) }}</h6>
                            <table class="table text-end">
                                <tr>
                                    <th class="text-start">Pcs</th>
                                    <th>:</th>
                                    <th>{{ number_format($pcs, 0) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-start">Gr</th>
                                    <th>:</th>
                                    <th>{{ number_format($gr, 0) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-start">Ttl Rp</th>
                                    <th>:</th>
                                    <th>{{ number_format($ttl_rp, 0) }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            @endforeach
        </div> --}}

        <x-theme.modal title="Detail Barang Opname" btnSave="T" size="modal-lg" idModal="detail">
            <div id="load_detail"></div>
        </x-theme.modal>
        @section('scripts')
            <script>
                $(document).on('click', '.detail', function() {
                    const no = $(this).attr('no')
                    $('#detail').modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('opname.detail') }}?no=" + no,
                        success: function(r) {
                            $("#load_detail").html(r);
                            loadTable('tblOpname')
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>
</x-theme.app>
