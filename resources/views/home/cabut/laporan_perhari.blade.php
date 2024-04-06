<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}
                    {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}
                    <span class="text-warning" style="font-size: 12px"><em>jika data tidak ada silahkan view dulu
                            !</em></span>
                </h6>
            </div>

            <div class="col-lg-6">
                @include('home.cabut.btn_export_global')
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.cetak.nav')
            <ul class="bg-info nav nav-pills float-start mt-4">
                @foreach ($pengawas as $d)
                    <li class="nav-item">
                        <a class="nav-link text-white {{ $d->id_pengawas == $id_pengawas ? 'active' : '' }}"
                            aria-current="page"
                            href="{{ route('cabut.laporan_perhari', [
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'id_pengawas' => $d->id_pengawas,
                            ]) }}">{{ $d->name }}</a>
                    </li>
                @endforeach
            </ul>

        </div>

    </x-slot>

    <x-slot name="cardBody">
        <style>
            .badge {
                cursor: pointer;
            }

            thead {
                position: sticky;
                top: 0;
                background-color: #f1f1f1;
                /* Warna latar belakang header yang tetap */
                z-index: 1;
            }
        </style>
        <h6>Pgws : {{ $nm_pengawas }}</h6>
        <section class="row">
            <div class="col-lg-12 mb-2">
                <table class="float-start">
                    <tbody>
                        <tr>
                            <td>Pencarian :</td>
                            <td><input autofocus type="text" id="pencarian" class="form-control float-end"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <style>
                tr td {
                    text-align: right
                }
            </style>
            <div class="col-lg-6">
                <table id="tblAldi" class="table table-stripped table-hover table-bordered table-responsive">
                    @php
                        $bgDanger = 'text-white bg-danger';
                        $buka =
                            "<span class='badge bg-secondary float-end'>Buka <i class='fas fa-caret-down'></i></span>";
                    @endphp
                    <thead>
                        <tr>
                            <th colspan="3">#</th>
                            <th colspan="7" class="text-center dhead">SELESAI</th>
                            <th></th>
                            <th colspan="5" class="text-center dhead">PROSES</th>
                            <th colspan="2" class="text-white bg-danger text-center">Selesai & Proses</th>
                        </tr>
                        <tr>
                            <th class="dhead">Nama</th>
                            <th class="dhead">Kelas</th>
                            <th class="dhead">Hari Masuk</th>
                            <th class="dhead">Cabut</th>
                            <th class="dhead">Eo</th>
                            <th class="dhead">Sortir</th>
                            <th class="dhead">Dll</th>
                            <th class="dhead">Denda</th>
                            <th class="dhead">Ttl Gaji Selesai</th>
                            <th class="dhead">Rata2</th>
                            <th class="bg-white"></th>
                            <th class="dhead">Cabut</th>
                            <th class="dhead">Eo</th>
                            <th class="dhead">Sortir</th>
                            <th class="dhead">Ttl Gaji Proses</th>
                            <th class="dhead">Rata2</th>
                            <th class="bg-danger text-white">Ttl</th>
                            <th class="bg-danger text-white">Rata2</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($tbl as $data)
                            <tr class="detail" style="cursor: pointer" bulan="{{ $bulan }}"
                                tahun="{{ $tahun }}" id_anak="{{ $data->id_anak }}">
                                <td class="text-start">{{ $data->nm_anak }}</td>
                                <td>{{ $data->kelas }}</td>
                                <td>{{ $data->hariMasuk }}</td>
                                <td>{{ number_format($data->ttl_rp, 0) }}</td>
                                <td>{{ number_format($data->eo_ttl_rp, 0) }}</td>
                                <td>{{ number_format($data->sortir_ttl_rp, 0) }}</td>
                                <td>{{ number_format($data->ttl_rp_dll, 0) }}</td>
                                <td>{{ number_format($data->ttl_rp_denda, 0) }}</td>
                                @php
                                    $ttl =
                                        $data->ttl_rp +
                                        $data->eo_ttl_rp +
                                        $data->sortir_ttl_rp +
                                        $data->ttl_rp_dll -
                                        $data->ttl_rp_denda;
                                    $rata = empty($data->hariMasuk) ? 0 : $ttl / $data->hariMasuk;

                                    $ttlProses = $data->rupiah + $data->eo_rp_target + $data->sortir_rp_target;
                                    $rataProses = empty($data->hariMasuk) ? 0 : $ttlProses / $data->hariMasuk;
                                    $rataSelesaiProses = empty($data->hariMasuk)
                                        ? 0
                                        : ($ttl + $ttlProses) / $data->hariMasuk;
                                @endphp
                                <td>{{ number_format($ttl, 0) }}</td>
                                <td>{{ number_format($rata, 0) }}</td>
                                <td class="bg-white"></td>
                                {{-- proses --}}
                                <td>{{ number_format($data->rupiah, 0) }}</td>
                                <td>{{ number_format($data->eo_rp_target, 0) }}</td>
                                <td>{{ number_format($data->sortir_rp_target, 0) }}</td>
                                <td>{{ number_format($ttlProses, 0) }}</td>
                                <td>{{ number_format($rataProses, 0) }}</td>
                                <td>{{ number_format($ttl + $ttlProses, 0) }}</td>
                                <td>{{ number_format($rataSelesaiProses, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- <tfoot class="bg-info text-white">
                    <tr>
                        <th>TOTAL</th>
                        <th></th>
                        <th></th>
                        <th></th>
                       
                        <th>{{ number_format($ttlTtlRp, 0) }}</th>
                        <th></th>
                    </tr>
                </tfoot> --}}
                </table>
            </div>



            <x-theme.modal btnSave="T" size="modal-lg" title="Detail Laporan" idModal="detail">
                <div class="loading d-none">
                    <x-theme.loading/>
                </div>
                <div id="detail_laporan_harian"></div>
            </x-theme.modal>

        </section>

        @section('scripts')
            <script>
                pencarian('pencarian', 'tblAldi')

                $(document).on('click', '.detail', function(e) {
                    e.preventDefault();
                    const id_anak = $(this).attr('id_anak')
                    const bulan = $(this).attr('bulan')
                    const tahun = $(this).attr('tahun')
                    $('#detail').modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.detail_laporan_harian') }}",
                        data: {
                            id_anak: id_anak,
                            bulan: bulan,
                            tahun: tahun,
                        },
                        beforeSend:function(){
                            $("#detail_laporan_harian").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#detail_laporan_harian").html(r);
                            $('#tableHarian').DataTable({
                                "paging": true,
                                "pageLength": 10,
                                "lengthChange": true,
                                "stateSave": true,
                                "searching": true,
                            });

                        }
                    });
                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
