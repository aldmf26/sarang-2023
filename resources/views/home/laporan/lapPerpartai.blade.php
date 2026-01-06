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
            <div class="col-lg-12">
                <div class="d-flex justify-content-between">
                    <div>
                        @include('home.laporan.nav')
                    </div>
                    <div>
                        <a href="#" class="btn btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#oprasional"><i class="fas fa-plus"></i> Cost Oprasional</a>
                        <a href="#" class="btn btn-primary float-end me-2" data-bs-toggle="modal"
                            data-bs-target="#view"><i class="fas fa-calendar-check"></i> View</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-3 mb-1">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <select name="show" id="" class="form-select show">
                            @foreach ($options as $opt)
                                <option {{ $opt == request()->get('show') ? 'selected' : '' }}
                                    value="{{ $opt === 'All' ? $total : $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        <label for="">per halaman</label>
                    </div>{{--
                    <div class="d-flex gap-2">
                        <div>
                            <input type="text" id="search" class="form-control form-control-sm searchInput"
                                style="width: 170px" placeholder="cari partai">
                        </div>
                         <div>
                            <button class="btn btn-primary btn-sm btnSearch" type="submit"><i
                                    class="fas fa-search"></i>
                                Cari</button>
                        </div> 
                    </div> --}}
                </div>
            </div>
            {{-- disini tablenya --}}
            <div class="col-lg-12">
                <div class="table-responsive">
                    <div class="tblhide">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="sticky">
                                <tr>
                                    <th class="dhead " rowspan="2">#</th>
                                    <th class="dhead" rowspan="2">partai</th>
                                    {{-- <th class="dhead" rowspan="2">no box</th> --}}
                                    <th class="text-center dhead" colspan="3">BK</th>
                                    <th class="text-center dhead" colspan="4">Cabut</th>
                                    <th class="text-center dhead" colspan="4">Eox/Eol</th>
                                    <th class="text-center dhead" colspan="4">Cetak</th>
                                    <th class="text-center dhead" colspan="4">Sortir</th>
                                    <th class="dhead text-end" rowspan="2">Cost bk</th>
                                    <th class="dhead text-end" rowspan="2">Cost cbt</th>
                                    <th class="dhead text-end" rowspan="2">Cost eox/eol</th>
                                    <th class="dhead text-end" rowspan="2">Cost ctk</th>
                                    <th class="dhead text-end" rowspan="2">Cost sortir</th>
                                    <th class="dhead text-end" rowspan="2">Cost cu</th>
                                    <th class="dhead text-end" rowspan="2">Cost dll</th>
                                    <th class="dhead text-end" rowspan="2">Cost oprasional</th>
                                    <th class="dhead text-end" rowspan="2">Total Rp</th>
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
                                        <td>{{ $partai->firstItem() + $no }}</td>
                                        <td>
                                            <a class="detailPartai" partai="{{ $p->nm_partai }}" href="#"
                                                data-bs-target="#detailPartai"
                                                data-bs-toggle="modal">{{ $p->nm_partai }}</a>
                                        </td>
                                        {{-- <td>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                                                class="detail no_box{{ $p->no_box ?? '-' }}"
                                                no_box="{{ $p->no_box ?? '-' }}" partai="{{ $p->nm_partai }}">
                                                <u>{{ $p->no_box ?? '-' }}</u>
                                            </a>
                                        </td> --}}
                                        <td class="text-end">{{ number_format($p->pcs_awal ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($p->gr_awal ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($p->hrga_satuan ?? 0, 0) }}</td>

                                        <td class="text-end">{{ number_format($p->pcs_cbt ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($p->gr_cbt ?? 0, 0) }}</td>
                                        <td class="text-end">
                                            {{ $p->hrga_satuan == 0 || empty($p->gr_cbt) ? 0 : number_format(($p->cost_bk + $p->cost_cbt) / $p->gr_cbt, 0) }}
                                        </td>
                                        <td class="text-end">{{ number_format($p->sst_cbt ?? 0) }} %</td>

                                        <td class="text-end">0</td>
                                        <td class="text-end">{{ number_format($p->gr_eo ?? 0, 0) }}</td>
                                        <td class="text-end">
                                            {{ number_format($p->rp_gram_eo ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($p->sst_eo ?? 0, 0) }} %</td>

                                        <td class="text-end">{{ number_format($p->pcs_ctk ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($p->gr_ctk ?? 0, 0) }}</td>
                                        <td class="text-end">
                                            {{ $p->hrga_satuan == 0 || empty($p->gr_ctk) ? 0 : number_format(($p->cost_bk + $p->cost_cbt + $p->cost_ctk) / $p->gr_ctk, 0) }}
                                        </td>
                                        <td class="text-end">{{ number_format($p->sst_ctk ?? 0, 0) }} %</td>

                                        <td class="text-end">{{ number_format($p->pcs_str ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($p->gr_str ?? 0, 0) }}</td>
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
                                        <td class="text-end">
                                            {{ number_format($p->harian_cbt + $p->harian_ctk + $p->harian_str + $p->harian_eo, 0) }}
                                            @php
                                                $harian =
                                                    $p->harian_cbt + $p->harian_ctk + $p->harian_str + $p->harian_eo;
                                                $cost_oprasional =
                                                    $p->oprasional_cbt +
                                                    $p->oprasional_ctk +
                                                    $p->oprasional_str +
                                                    $p->oprasional_eo;

                                            @endphp
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($cost_oprasional, 0) }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($cost_oprasional + $p->cost_bk + $p->cost_cbt + $p->cost_ctk + $p->cost_str + $p->cost_cu + $harian + $p->cost_eo, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between">
                            {{ $partai->links() }}
                            <p><b>Total : {{ number_format($partai->total(), 0) }} Data</b></p>
                        </div>
                    </div>
                    <div id="loadSearch"></div>
                </div>
                @include('components.theme.loading')
            </div>

            <x-theme.modal title="Detail Box" idModal="detail" size="modal-lg-max" btnSave="T">
                <div class="row">
                    <div id="load_detail"></div>
                </div>
            </x-theme.modal>

            <x-theme.modal title="Detail Partai" idModal="detailPartai" size="modal-lg-max" btnSave="T">
                <div id="load_detail_partai"></div>
                @include('components.theme.loading')

            </x-theme.modal>

            <form action="" method="get">
                <x-theme.modal title="View" idModal="view" size="modal-lg-sm" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="">View bulan</label>
                            <select name="bulan" id="" class="form-control">
                                @foreach ($bulandata as $b)
                                    <option value="{{ $b->id_bulan }}"
                                        {{ $b->id_bulan == $bulan ? 'selected' : '' }}>
                                        {{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="">View tahun</label>
                            <select name="tahun" id="" class="form-control">
                                <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                            </select>
                        </div>
                    </div>
                </x-theme.modal>
            </form>

            <form action="{{ route('laporanakhir.saveoprasional') }}" method="post">
                @csrf
                <x-theme.modal title="Oprasional" idModal="oprasional" size="modal-lg" btnSave="Y">
                    <div class="row">
                        {{-- <div class="col-lg-3 mb-3">
                            <label for="">Pilih Bulan</label>
                            <select name="bulan" id="" class="form-control">
                                @foreach ($bulandata as $b)
                                    <option value="{{ $b->id_bulan }}"
                                        {{ $b->id_bulan == $bulan ? 'selected' : '' }}>{{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-lg-12">
                            {{-- <h5>Gaji : {{ number_format($gaji->ttl_rp, 0) }}</h5> --}}
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead text-end">Gr Akhir Cbt</th>
                                        <th class="dhead text-end">Gr Akhir Eo</th>
                                        <th class="dhead text-end">Gr Akhir Ctk</th>
                                        <th class="dhead text-end">Gr Akhir Sortir</th>
                                        <th class="dhead text-end">Gaji</th>
                                        <th class="dhead text-end">Cost Oprasional</th>
                                        <th class="dhead text-end" width="150px">Total Oprasional</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-end">{{ number_format($cabutGrAkhir, 0) }}</td>
                                        <td class="text-end">{{ number_format($gr_eo_akhir, 0) }}</td>
                                        <td class="text-end">{{ number_format($ctk ?? 0, 0) }}</td>
                                        <td class="text-end">{{ number_format($str, 0) }}</td>
                                        <td class="text-end">{{ number_format($gaji->ttl_gaji, 0) }}</td>
                                        <td class="text-end">{{ number_format($oprasional->rp_oprasional ?? 0, 0) }}
                                        </td>
                                        <input type="hidden" name="gaji" value="{{ $gaji->ttl_gaji ?? 0 }}">


                                        <td x-data="numberFormat({{ empty($oprasional->rp_oprasional) ? 0 : $oprasional->rp_oprasional + $gaji->ttl_gaji }})">
                                            <input type="text" class="form-control" autofocus
                                                name="biaya_oprasional" id="number" x-model="formattedNumber"
                                                @keyup="formatNumber"
                                                value="{{ empty($oprasional->rp_oprasional) ? 0 : $oprasional->rp_oprasional + $gaji->ttl_gaji }}">

                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-lg-12">
                            @php
                                $total = $cabutGrAkhir + $gr_eo_akhir + $ctk + $str;
                            @endphp
                            <h6 class="">
                                Total :
                                {{ number_format($cabutGrAkhir + $gr_eo_akhir + $ctk + $str, 0) }}
                                | Rp/gr :
                                {{ empty($oprasional->rp_oprasional) ? 0 : number_format(($oprasional->rp_oprasional + $gaji->ttl_gaji) / $total, 0) }}
                            </h6>


                        </div>
                        <div class="col-lg-12">

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
                // fitur show data
                $('.show').change(function() {
                    var showVal = $(this).val();
                    var currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('show', showVal);

                    // Pastikan page diatur kembali ke 1 setiap kali parameter show berubah
                    currentUrl.searchParams.set('page', 1);

                    window.location.href = currentUrl.toString();
                });

                // fitur search
                $('.loading').hide();

                var timer = null;
                $(document).on('keyup', '.searchInputdsa', function() {
                    clearTimeout(timer);
                    var search = $(this).val();
                    timer = setTimeout(function() {
                        if (search.length === 0) {
                            $('.tblhide').show();
                            $('#loadSearch').hide();
                        } else {
                            $('.tblhide').hide();
                            if ($('.table_cari').length == 0) {
                                $.ajax({
                                    type: "GET",
                                    url: "{{ route('laporanakhir.search') }}?search=" + search,
                                    beforeSend: function() {
                                        $('.loading').show();
                                    },
                                    success: function(r) {
                                        $('.loading').hide();
                                        $('#loadSearch').html(r);
                                    },
                                    error: function(xhr, ajaxOptions, thrownError) {
                                        alert(xhr.status + "\n" + xhr.responseText + "\n" +
                                            thrownError);
                                    }
                                });
                            }
                        }
                    }, 500);
                })

                $(document).on('click', '.btnSearch', function() {
                    var search = $('#search').val();
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        if (search.length === 0) {
                            $('.tblhide').show();
                            $('#loadSearch').hide();
                        } else {
                            $('.tblhide').hide();
                            if ($('.table_cari').length == 0) {
                                $.ajax({
                                    type: "GET",
                                    url: "{{ route('laporanakhir.search') }}?search=" + search,
                                    beforeSend: function() {
                                        $('.loading').show();
                                    },
                                    success: function(r) {
                                        $('.loading').hide();
                                        $('#loadSearch').html(r);
                                    },
                                    error: function(xhr, ajaxOptions, thrownError) {
                                        alert(xhr.status + "\n" + xhr.responseText + "\n" +
                                            thrownError);
                                    }
                                });
                            }
                        }
                    }, 500);
                })

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
                $(document).on("click", ".detailPartai", function() {
                    var partai = $(this).attr("partai");

                    $.ajax({
                        type: "get",
                        url: `{{ route('laporanakhir.detail') }}?nm_partai=${partai}`,
                        beforeSend: function() {
                            $('.loading').show();
                            $("#load_detail_partai").hide();
                        },
                        success: function(response) {
                            $('.loading').hide();
                            $("#load_detail_partai").show()
                            $("#load_detail_partai").html(response);
                            loadTable("tblPartai");
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
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
