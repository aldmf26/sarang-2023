<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">

        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} , Bulan:
                    {{ date('F', strtotime('01-' . $bulan . '-2025')) }}</h6>
            </div>
            {{-- <div class="col-lg-6">
                <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus"
                    addClass="float-end tambah_kerja ms-2" teks="Oprasional" />
                @include('home.cabut.view_bulandibayar')
            </div> --}}

        </div>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8">
                @include('home.laporan.nav')
            </div>

            <div class="col-lg-10"></div>
            <div class="col-lg-2">
                <input type="text" id="pencarian" class="form-control form-control-sm mb-2 float-end"
                    style="width: 170px" placeholder="cari">
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered" id="table_cari" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead ">#</th>
                            <th class="dhead">partai</th>
                            <th class="dhead text-end">pcs awal</th>
                            <th class="dhead text-end">gr awal</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">cost bk</th>
                            <th class="dhead text-end">pcs timbang ulang</th>
                            <th class="dhead text-end">gr timbang ulang</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">cost bk</th>
                            <th class="dhead text-end">cost cabut</th>
                            <th class="dhead text-end">cost eo</th>
                            <th class="dhead text-end">cost cetak</th>
                            <th class="dhead text-end">cost sortir</th>
                            <th class="dhead text-end">cost cu</th>
                            <th class="dhead text-end">cost dll</th>
                            <th class="dhead text-end">cost oprasional</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partai as $p)
                            @php
                                $ket = $p->nm_partai;
                                $resSum = Cache::remember('partai' . $ket, now()->addMinutes(5), function () use (
                                    $ket,
                                ) {
                                    return Http::get('gudangsarang.ptagafood.com/api/apibk/partai', [
                                        'nm_partai' => $ket,
                                    ])->object();
                                });
                                $c = $resSum;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nm_partai ?? '' }}</td>
                                <td class="text-end">{{ number_format($c->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($c->gr ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($c->rupiah ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format(empty($c->rupiah) ? 0 : $c->rupiah * $c->gr, 0) }}
                                </td>

                                <td class="text-end">{{ number_format($p->pcs_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->gr_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->ttl_rp_bk / $p->gr_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->ttl_rp_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_cbt, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_eo, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_ctk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_str, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <x-theme.modal title="Detail Box" idModal="detail" size="modal-lg-max" btnSave="T">
                <div class="row">
                    <div id="load_detail"></div>
                </div>
            </x-theme.modal>



        </section>

        @section('js')
            <script>
                $(document).on("click", ".detail", function() {
                    var no_box = $(this).attr("no_box");
                    var partai = $(this).attr("partai");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail') }}",
                        data: {
                            no_box: no_box,
                            partai: partai
                        },
                        success: function(response) {
                            $("#load_detail").html(response);
                        }
                    });

                });
                pencarian('pencarian', 'table_cari')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
