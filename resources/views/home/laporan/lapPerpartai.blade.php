<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">

        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} , Bulan:
                    {{ date('F', strtotime('01-' . $bulan . '-2024')) }}</h6>
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
            <div class="col-lg-2 mb-2">
                <a href="#" class="btn btn-primary float-end" data-bs-toggle="modal"
                    data-bs-target="#oprasional"><i class="fas fa-plus"></i> Cost Oprasional</a>
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
                            <th class="dhead " rowspan="2">#</th>
                            <th class="dhead" rowspan="2">partai</th>
                            <th class="dhead" rowspan="2">no box</th>
                            <th class="text-center dhead" colspan="3">BK</th>
                            <th class="text-center dhead" colspan="4">Cabut</th>
                            <th class="text-center dhead" colspan="4">Eox/Eol</th>
                            <th class="text-center dhead" colspan="4">Cetak</th>
                            <th class="text-center dhead" colspan="4">Sortir</th>
                            <th class="dhead" rowspan="2">Cost bk</th>
                            <th class="dhead" rowspan="2">Cost cbt</th>
                            <th class="dhead" rowspan="2">Cost eox/eol</th>
                            <th class="dhead" rowspan="2">Cost ctk</th>
                            <th class="dhead" rowspan="2">Cost sortir</th>
                            <th class="dhead" rowspan="2">Cost cu</th>
                            <th class="dhead" rowspan="2">Cost dll</th>
                            <th class="dhead" rowspan="2">Cost oprasional</th>
                            <th class="dhead" rowspan="2">Total Rp</th>
                        </tr>
                        <tr>
                            <th class="dhead text-end">pcs awal</th>
                            <th class="dhead text-end">gr awal</th>
                            <th class="dhead text-end">rp/gr</th>

                            <th class="dhead text-end">pcs akhir</th>
                            <th class="dhead text-end">gr akhir</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">susut%</th>

                            <th class="dhead text-end">pcs akhir</th>
                            <th class="dhead text-end">gr akhir</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">susut%</th>

                            <th class="dhead text-end">pcs akhir</th>
                            <th class="dhead text-end">gr akhir</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">susut%</th>

                            <th class="dhead text-end">pcs akhir</th>
                            <th class="dhead text-end">gr akhir</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">susut%</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partai as $no => $p)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $p->nm_partai }}</td>
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                                        class="detail no_box{{ $p->no_box }}" no_box="{{ $p->no_box }}"
                                        partai="{{ $p->nm_partai }}">
                                        <u>{{ $p->no_box }}</u>
                                    </a>
                                </td>
                                <td class="text-end">{{ $p->pcs_awal }}</td>
                                <td class="text-end">{{ $p->gr_awal }}</td>
                                <td class="text-end">{{ number_format($p->hrga_satuan, 0) }}</td>

                                <td class="text-end">{{ $p->pcs_cbt }}</td>
                                <td class="text-end">{{ $p->gr_cbt }}</td>
                                <td class="text-end">
                                    {{ $p->hrga_satuan == 0 || empty($p->gr_cbt) ? 0 : number_format(($p->cost_bk + $p->cost_cbt) / $p->gr_cbt, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($p->sst_cbt, 0) }} %</td>

                                <td class="text-end">0</td>
                                <td class="text-end">{{ $p->gr_eo ?? 0 }}</td>
                                <td class="text-end">
                                    {{ number_format($p->rp_gram_eo ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($p->sst_eo ?? 0, 0) }} %</td>

                                <td class="text-end">{{ $p->pcs_ctk }}</td>
                                <td class="text-end">{{ $p->gr_ctk }}</td>
                                <td class="text-end">
                                    {{ $p->hrga_satuan == 0 || empty($p->gr_ctk) ? 0 : number_format(($p->cost_bk + $p->cost_cbt + $p->cost_ctk) / $p->gr_ctk, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($p->sst_ctk, 0) }} %</td>

                                <td class="text-end">{{ $p->pcs_str }}</td>
                                <td class="text-end">{{ $p->gr_str }}</td>
                                <td class="text-end">
                                    {{ $p->hrga_satuan == 0 || empty($p->gr_str) ? 0 : number_format(($p->cost_bk + $p->cost_cbt + $p->cost_ctk + $p->cost_str) / $p->gr_str, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($p->sst_str, 0) }} %</td>

                                <td class="text-end">{{ number_format($p->cost_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_cbt, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_eo, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_ctk, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_str, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_cu, 0) }}</td>
                                <td class="text-end">{{ number_format($p->cost_dll, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($p->oprasional_cbt) ? 0 : number_format($p->oprasional_cbt + $p->oprasional_ctk + $p->oprasional_str + $p->oprasional_cu + $p->oprasional_eo, 0) }}
                                </td>
                                @php
                                    $cost_oprasional = empty($p->oprasional_cbt)
                                        ? 0
                                        : $p->oprasional_cbt +
                                            $p->oprasional_ctk +
                                            $p->oprasional_str +
                                            $p->oprasional_cu +
                                            $p->oprasional_eo;
                                @endphp
                                <td>{{ number_format($cost_oprasional + $p->cost_bk + $p->cost_cbt + $p->cost_ctk + $p->cost_str + $p->cost_cu + $p->cost_dll + $p->cost_eo, 0) }}
                                </td>
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

            <form action="{{ route('laporanakhir.saveoprasional') }}" method="post">
                @csrf
                <x-theme.modal title="Oprasional" idModal="oprasional" size="modal-lg" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <label for="">Pilih Bulan</label>
                            <select name="" id="" class="form-control">
                                @foreach ($bulandata as $b)
                                    <option value="{{ $b->id_bulan }}">{{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead text-end">Gr Akhir Cbt</th>
                                        <th class="dhead text-end">Gr Akhir Eo</th>
                                        <th class="dhead text-end">Gr Akhir Ctk</th>
                                        <th class="dhead text-end">Gr Akhir Sortir</th>
                                        <th class="dhead text-end">Gr Akhir Cu</th>
                                        <th class="dhead text-end" width="150px">Cost Oprasional</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-end">{{ number_format($cabut->gr_akhir, 0) }}</td>
                                        <td class="text-end">{{ number_format($eo->gr_eo_akhir, 0) }}</td>
                                        <td class="text-end">{{ number_format($ctk->gr_akhir, 0) }}</td>
                                        <td class="text-end">{{ number_format($str->gr_akhir, 0) }}</td>
                                        <td class="text-end">{{ number_format($cu->gr_akhir, 0) }}</td>
                                        <td x-data="numberFormat({{ $oprasional->rp_oprasional ?? 0 }})">
                                            <input type="text" class="form-control" autofocus
                                                name="biaya_oprasional" id="number" x-model="formattedNumber"
                                                @keyup="formatNumber" value="{{ $oprasional->rp_oprasional ?? 0 }}">

                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-lg-2">

                            <h6 class="">
                                Total :
                                {{ number_format($cabut->gr_akhir + $eo->gr_eo_akhir + $ctk->gr_akhir + $str->gr_akhir + $cu->gr_akhir, 0) }}
                            </h6>
                            @php
                                $total =
                                    $cabut->gr_akhir +
                                    $eo->gr_eo_akhir +
                                    $ctk->gr_akhir +
                                    $str->gr_akhir +
                                    $cu->gr_akhir;
                            @endphp

                        </div>
                        <div class="col-lg-10">

                        </div>
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ date('Y') }}">
                        <input type="hidden" name="gr_akhir" value="{{ $total }}">

                    </div>
                </x-theme.modal>
            </form>



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
