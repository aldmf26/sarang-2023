<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8">
                @include('home.laporan.nav')
            </div>
            <div class="col-lg-12">
                <table class="table" id="tableScroll">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Partai</th>
                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Gr Awal</th>
                            <th class="dhead text-end">Cost BK</th>
                            <th class="dhead text-end">Cost Cbt</th>
                            <th class="dhead text-end">Cost Ctk</th>
                            <th class="dhead text-end">Cost Str</th>
                            <th class="dhead text-end">Cost Oprasional</th>
                            <th class="dhead text-end">Ttl Cost</th>
                            <th class="dhead text-end">Pcs Akhir</th>
                            <th class="dhead text-end">Gr Akhir</th>
                            <th class="dhead text-end">Sst%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partai as $no => $p)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $p->nm_partai }}</td>
                                <td class="text-end">{{ number_format($p->pcs_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($p->gr_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($p->ttl_rp, 0) }}</td>
                                <td class="text-end">
                                    <a href="#" class="detail_cabut" data-bs-toggle="modal"
                                        partai="{{ $p->nm_partai }}" data-bs-target="#detail_cabut">
                                        {{ number_format($p->cost_cbt + $p->cost_eo, 0) }}
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="detail_cetak" data-bs-toggle="modal"
                                        partai="{{ $p->nm_partai }}" data-bs-target="#detail_cetak">
                                        {{ number_format($p->cost_ctk, 0) }}
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="detail_sortir" data-bs-toggle="modal"
                                        partai="{{ $p->nm_partai }}" data-bs-target="#detail_sortir">
                                        {{ $p->cost_sortir }}
                                    </a>
                                </td>
                                <td class="text-end">0</td>
                                <td class="text-end">
                                    {{ number_format($p->ttl_rp + $p->cost_ctk + $p->cost_eo + $p->cost_cbt + $p->cost_sortir, 0) }}
                                </td>
                                <td class="text-end">
                                    <a href="#" data-bs-toggle="modal" class="akhir" data-bs-target="#akhir"
                                        partai="{{ $p->nm_partai }}">{{ number_format($p->pcs_akhir ?? 0, 0) }}</a>
                                </td>
                                <td class="text-end">
                                    <a href="#" data-bs-toggle="modal" class="akhir" data-bs-target="#akhir"
                                        partai="{{ $p->nm_partai }}">{{ number_format($p->gr_akhir ?? 0, 0) }}</a>
                                </td>
                                <td class="text-end">{{ number_format((1 - $p->gr_akhir / $p->gr_awal) * 100, 0) }} %
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <form action="{{ route('laporanakhir.save_bk_akhir') }}" method="post">
                @csrf
                <x-theme.modal title="Gr Akhir" idModal="akhir" btnSave="Y">
                    <div id="load_akhir"></div>
                </x-theme.modal>
            </form>

            <x-theme.modal title="Detail Cabut" idModal="detail_cabut" btnSave="T" size="modal-lg-max">
                <div id="load_detail_cabut"></div>
            </x-theme.modal>

            <x-theme.modal title="Detail Cetak" idModal="detail_cetak" btnSave="T" size="modal-lg-max">
                <div id="load_detail_cetak"></div>
            </x-theme.modal>

            <x-theme.modal title="Detail Sortir" idModal="detail_sortir" btnSave="T" size="modal-lg-max">
                <div id="load_detail_sortir"></div>
            </x-theme.modal>

        </section>




        @section('js')
            <script>
                $(document).on("click", ".akhir", function() {
                    var partai = $(this).attr("partai");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_bk_akhir') }}",
                        data: {
                            partai: partai
                        },
                        success: function(response) {
                            $("#load_akhir").html(response);
                        }
                    });

                });
                $(document).on("click", ".detail_cetak", function() {
                    var partai = $(this).attr("partai");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail_cetak') }}",
                        data: {
                            partai: partai
                        },
                        success: function(response) {
                            $("#load_detail_cetak").html(response);
                        }
                    });

                });
                $(document).on("click", ".detail_cabut", function() {
                    var partai = $(this).attr("partai");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail_cabut') }}",
                        data: {
                            partai: partai
                        },
                        success: function(response) {
                            $("#load_detail_cabut").html(response);
                        }
                    });

                });
                $(document).on("click", ".detail_sortir", function() {
                    var partai = $(this).attr("partai");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail_sortir') }}",
                        data: {
                            partai: partai
                        },
                        success: function(response) {
                            $("#load_detail_sortir").html(response);
                        }
                    });

                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
