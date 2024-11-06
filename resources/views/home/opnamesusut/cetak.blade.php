<x-theme.app title="{{ $title }}" table="Y" sizeCard="10" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-12">
                @include('home.opnamesusut.nav')
                <table class="table table-bordered table-hover ">
                    <thead>
                        <tr>
                            <th rowspan="2" class="dhead">Nama Pengawas</th>
                            <th colspan="2" class="dhead text-center">Cabut proses</th>
                            <th colspan="2" class="dhead text-center">Cabut sisa pengawas</th>
                            <th colspan="2" class="dhead text-center">Cabut selesai siap cetak</th>
                            <th colspan="2" class="dhead text-center">Total</th>
                            <th colspan="3" class="dhead text-center">Susut</th>
                        </tr>
                        <tr>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            {{-- <th class="dhead text-center">nama posisi</th> --}}
                            <th class="dhead text-center">kelas</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">%</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $nama_terisi = [];
                            $current_name = null;
                            $current_total = 0;
                        @endphp

                        @foreach ($pgws_cabut as $p)
                            @if ($current_name !== $p->name)
                                @if ($current_name !== null)
                                    <!-- Menampilkan total untuk nama sebelumnya setelah grup selesai -->
                                    <tr>
                                        <td colspan="10" class="text-center dhead text-white"><strong>Total
                                                {{ $current_name }}</strong></td>
                                        <td class="text-end dhead text-white">
                                            <strong>{{ number_format($current_total, 0) }}</strong>
                                        </td>
                                        <td class="text-end dhead text-white">

                                        </td>
                                    </tr>
                                @endif

                                <!-- Reset total dan ganti nama saat ini ke nama baru -->
                                @php
                                    $current_name = $p->name;
                                    $current_total = 0;
                                @endphp
                            @endif

                            <tr>
                                @if (!in_array($p->name, $nama_terisi))
                                    <td>{{ $p->name }}</td>
                                    <td class="text-end">{{ number_format($p->pcs_proses, 0) }}</td>
                                    <td class="text-end">{{ number_format($p->gr_proses, 0) }}</td>
                                    <td class="text-end">{{ number_format($p->pcs_sisa, 0) }}</td>
                                    <td class="text-end">{{ number_format($p->gr_sisa, 0) }}</td>
                                    <td class="text-end">{{ number_format($p->pcs_siap_cetak, 0) }}</td>
                                    <td class="text-end">{{ number_format($p->gr_siap_cetak, 0) }}</td>
                                    <td class="text-end">
                                        {{ number_format($p->pcs_proses + $p->pcs_sisa + $p->pcs_siap_cetak, 0) }}</td>
                                    <td class="text-end">
                                        {{ number_format($p->gr_proses + $p->gr_sisa + $p->gr_siap_cetak, 0) }}</td>
                                    @php
                                        $nama_terisi[] = $p->name;
                                    @endphp
                                @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif





                                @php
                                    $susut = (1 - $p->gr_akhir / $p->gr_awal) * 100;
                                @endphp
                                {{-- <td class="{{ $susut > $p->batas_susut ? 'text-danger' : '' }}">Cabut
                                    {{ $p->name }}</td> --}}
                                <td class="{{ $susut > $p->batas_susut ? 'text-danger' : '' }}">{{ $p->tipe }}
                                </td>

                                @php
                                    $difference = $p->gr_awal - $p->gr_akhir;
                                    $current_total += $difference; // Tambahkan perbedaan ke total untuk nama saat ini
                                @endphp

                                <td class="text-end {{ $susut > $p->batas_susut ? 'text-danger' : '' }}">
                                    {{ number_format($difference, 0) }}</td>

                                <td
                                    class="text-end fw-bold text-decoration-underline {{ $susut > $p->batas_susut ? 'text-danger' : '' }}">
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#detail_data"
                                        tipe="{{ $p->tipe }}" id_pengawas="{{ $p->id_pengawas }}"
                                        class="{{ $susut > $p->batas_susut ? 'text-danger' : '' }} detail_data">{{ number_format((1 - $p->gr_akhir / $p->gr_awal) * 100, 0) }}
                                        %
                                    </a>

                                </td>
                                <!-- Kolom kosong untuk menghindari tampilan total sementara -->
                            </tr>
                        @endforeach

                        <!-- Menampilkan total untuk nama terakhir setelah looping selesai -->
                        @if ($current_name !== null)
                            <tr>
                                <td colspan="10" class="text-center dhead text-white"><strong>Total
                                        {{ $current_name }}</strong>
                                </td>
                                <td class="text-end dhead text-white">
                                    <strong>{{ number_format($current_total, 0) }}</strong>
                                </td>
                                <td class="text-end dhead text-white">

                                </td>
                            </tr>
                        @endif


                    </tbody>
                </table>

            </div>

            <div class="modal fade" id="detail_data" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog  modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Susut</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="get_detail"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
        </section>








        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
            </script>
            <script>
                get_opr();

                function get_opr() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('summary.get_operasional') }}",
                        success: function(response) {
                            $('#cost_opr').html(response);
                        }
                    });
                }
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
            <script>
                $(document).ready(function() {
                    $('.detail_data').click(function(e) {
                        e.preventDefault();
                        var tipe = $(this).attr('tipe');
                        var id_pengawas = $(this).attr('id_pengawas');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.detail_cabut') }}",
                            data: {
                                tipe: tipe,
                                id_pengawas: id_pengawas
                            },
                            success: function(response) {
                                $('#get_detail').html(response);

                            }
                        });

                    });
                });
            </script>
        @endsection

    </x-slot>
</x-theme.app>
