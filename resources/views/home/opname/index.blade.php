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
                                $ttl_rp = $d['body']['ttl_rp'] ?? 0;
                            @endphp
                            <div class="col-md-4">
                                <div no="{{ $i + 1 }}"
                                    class=" position-relative card cardHover pointer text-center border border-secondary">
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
                                        <div
                                            class="position-absolute top-50 start-50 translate-middle p-2 bg-transparent">
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
