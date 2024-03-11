<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
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
                    @foreach ($cards as $i => $d)
                        @if ($i < 6)
                            @php
                                $pcs = $d['body']['pcs'] ?? 0;
                                $gr = $d['body']['gr'] ?? 0;
                                $pcs_sst = $d['body']['pcs_sst'] ?? 0;
                                $gr_sst = $d['body']['gr_sst'] ?? 0;
                                $ttl_rp = $d['body']['ttl_rp'] ?? 0;
                            @endphp
                            <div class="col-md-4">
                                <div no="{{ $i + 1 }}"
                                    class="detail position-relative card cardHover pointer text-center border border-secondary">
                                    <div class="card-body">
                                        <h5>{{ $i + 1 }}</h5>
                                        <h6>{{ strtoupper($d['title']) }}</h6>
                                        <table class="table text-end">
                                            <tr>
                                                <th class="text-start">Pcs</th>
                                                <th>:</th>
                                                <th>{{ number_format($pcs, 0) }}</th>
                                            </tr>
                                            @if ($i+1 == 2)
                                            <tr>
                                                <th class="text-start">Pcs Sst</th>
                                                <th>:</th>
                                                <th>{{ number_format($pcs_sst, 0) }}</th>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th class="text-start">Gr</th>
                                                <th>:</th>
                                                <th>{{ number_format($gr, 0) }}</th>
                                            </tr>
                                            @if ($i+1 == 2)
                                            <tr>
                                                <th class="text-start">Gr Sst</th>
                                                <th>:</th>
                                                <th>{{ number_format($gr_sst, 0) }}</th>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th class="text-start">Ttl Rp</th>
                                                <th>:</th>
                                                <th>{{ number_format($ttl_rp, 0) }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                    @if (str_contains(strtolower($d['title']), 'sisa'))
                                        <div
                                            class="position-absolute top-50 start-50 translate-middle p-2 bg-transparent">
                                            <h6 style="transform: rotate(-15deg);" class="text-muted mb-0"
                                                style="font-style: italic;">Diopname</h6>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
        <hr>
        <div class="row">
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
        </div>
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

        <x-theme.modal title="Detail Barang Opname" size="modal-lg" idModal="detail">
            <div id="load_detail"></div>
        </x-theme.modal>

        <div class="loading">
            <div class="mb-2">
                <div class="modal fade text-left show" id="backdrop" tabindex="-1" aria-labelledby="myModalLabel4"
                    data-bs-backdrop="false" style="display: block; padding-right: 17px;" aria-modal="true"
                    role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel4">Harap Tunggu</h4>
                            </div>
                            <div class="modal-body">
                                <div class="progress" style="display:none;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                        style="width: 0%"></div>
                                </div>
                                <button class="btn-block btn btn-primary" type="button" disabled="">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    Loading...
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        @section('scripts')
            <script>
                $('.loading').hide()
                $(window).on('beforeunload', function() {
                    $('.loading').show()

                });
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
