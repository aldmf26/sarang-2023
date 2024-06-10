<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        @php
            $ttl_gr = 0;
            foreach ($partai as $p) {
                $ttl_gr += $p->gr_cbt + $p->gr_akhir_eo;
            }

            $rp = empty($oprasional->rupiah) ? 0 : $oprasional->rupiah / $ttl_gr;
        @endphp
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} , Bulan:
                    {{ date('F', strtotime('01-' . $bulan . '-2024')) }}</h6>
            </div>
            <div class="col-lg-6">
                <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus"
                    addClass="float-end tambah_kerja ms-2" teks="Oprasional" />
                @include('home.cabut.view_bulandibayar')
            </div>
            <div class="col-lg-6">
                <p class="float-start mt-1">Cost Oprasional : {{ number_format($oprasional->rupiah ?? 0, 0) }}</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            {{-- <div class="col-lg-8">
                @include('home.laporan.nav')
            </div> --}}

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
                            @php
                                $cost_bk = ($p->ttl_rp / $p->gr_awal) * ($p->gr_cbt + $p->gr_akhir_eo);
                            @endphp
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $p->nm_partai }}</td>
                                <td class="text-end">{{ number_format($p->pcs_cbt, 0) }}</td>
                                <td class="text-end">{{ number_format($p->gr_cbt + $p->gr_akhir_eo, 0) }}</td>
                                <td class="text-end">
                                    {{ number_format($cost_bk, 0) }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="detail_cabut" data-bs-toggle="modal"
                                        partai="{{ $p->nm_partai }}" bulan="{{ $bulan }}"
                                        data-bs-target="#detail_cabut">
                                        {{ number_format($p->cost_cbt + $p->cost_eo, 0) }}
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="detail_cetak" data-bs-toggle="modal"
                                        partai="{{ $p->nm_partai }}" bulan="{{ $bulan }}"
                                        data-bs-target="#detail_cetak">
                                        {{ number_format($p->cost_ctk, 0) }}
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="detail_sortir" data-bs-toggle="modal"
                                        partai="{{ $p->nm_partai }}" bulan="{{ $bulan }}"
                                        data-bs-target="#detail_sortir">
                                        {{ number_format($p->cost_sortir, 0) }}
                                    </a>
                                </td>
                                <td class="text-end">{{ number_format($rp * ($p->gr_cbt + $p->gr_akhir_eo), 0) }} </td>
                                <td class="text-end">
                                    {{ number_format($cost_bk + $p->cost_ctk + $p->cost_eo + $p->cost_cbt + $p->cost_sortir + $rp * ($p->gr_cbt + $p->gr_akhir_eo), 0) }}
                                </td>
                                <td class="text-end">
                                    <a href="#" data-bs-toggle="modal" class="akhir" data-bs-target="#akhir"
                                        partai="{{ $p->nm_partai }}"
                                        bulan="{{ $bulan }}">{{ number_format($p->pcs_akhir ?? 0, 0) }}</a>
                                </td>
                                <td class="text-end">
                                    <a href="#" data-bs-toggle="modal" class="akhir" data-bs-target="#akhir"
                                        partai="{{ $p->nm_partai }}"
                                        bulan="{{ $bulan }}">{{ number_format($p->gr_akhir ?? 0, 0) }}</a>
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

            <form action="{{ route('laporanakhir.save_oprasional') }}" method="post">
                @csrf
                <x-theme.modal title="Tambah Oprasional" idModal="tambah">
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="">Total Rp</label>
                            <input type="text" class="form-control" name="total_rp">
                        </div>
                        <div class="col-lg-4">
                            <label for="">Bulan dibayar</label>
                            <select name="bulan_dibayar" class="form-control select2" id="">
                                <option value="">- Pilih Bulan -</option>
                                @php
                                    $bulan = DB::table('bulan')->get();
                                @endphp
                                @foreach ($bulan as $b)
                                    <option {{ $b->bulan == date('m') ? 'selected' : '' }}
                                        value="{{ $b->bulan }}">
                                        {{ strtoupper($b->nm_bulan) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="">Tahun</label>
                            <select name="tahun_dibayar" class="form-control select2" id="">
                                <option value="">- Pilih Tahun -</option>
                                @php
                                    $tahun = [2022, 2023, 2024];
                                @endphp
                                @foreach ($tahun as $b)
                                    <option {{ $b == date('Y') ? 'selected' : '' }} value="{{ $b }}">
                                        {{ strtoupper($b) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </x-theme.modal>
            </form>

        </section>




        @section('js')
            <script>
                $(document).on("click", ".akhir", function() {
                    var partai = $(this).attr("partai");
                    var bulan = $(this).attr("bulan");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_bk_akhir') }}",
                        data: {
                            partai: partai,
                            bulan: bulan
                        },
                        success: function(response) {
                            $("#load_akhir").html(response);
                        }
                    });

                });
                $(document).on("click", ".detail_cetak", function() {
                    var partai = $(this).attr("partai");
                    var bulan = $(this).attr("bulan");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail_cetak') }}",
                        data: {
                            partai: partai,
                            bulan: bulan
                        },
                        success: function(response) {
                            $("#load_detail_cetak").html(response);
                        }
                    });

                });
                $(document).on("click", ".detail_cabut", function() {
                    var partai = $(this).attr("partai");
                    var bulan = $(this).attr("bulan");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail_cabut') }}",
                        data: {
                            partai: partai,
                            bulan: bulan
                        },
                        success: function(response) {
                            $("#load_detail_cabut").html(response);
                        }
                    });

                });
                $(document).on("click", ".detail_sortir", function() {
                    var partai = $(this).attr("partai");
                    var bulan = $(this).attr("bulan");
                    $.ajax({
                        type: "get",
                        url: "{{ route('laporanakhir.get_detail_sortir') }}",
                        data: {
                            partai: partai,
                            bulan: bulan
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
