<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-12">
                <div>
                    <h6>Laporan Partai</h6>
                </div>
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Partai</th>
                            <th class="dhead ">Grade</th>
                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Gr Awal</th>
                            <th class="dhead text-end">Modal Awal</th>
                            <th class="dhead text-end">Pcs Akhir</th>
                            <th class="dhead text-end">Gr Akhir</th>
                            <th class="dhead text-end">Susut</th>
                            <th class="dhead text-end">Modal + Gaji + Cost operasional</th>
                            <th class="dhead text-end">Rata-rata</th>
                            <th class="dhead ">Status</th>
                            <th class="dhead ">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bk as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>{{ $b->tipe }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($b->modal_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($b->pcs_akhir, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr_akhir, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($b->gr_akhir) ? 0 : number_format((1 - $b->gr_akhir / $b->gr) * 100, 0) }}%
                                </td>
                                <td class="text-end">
                                    {{ number_format($b->modal_awal + ($b->cost_kerja ?? 0) + ($b->cost_op ?? 0), 0) }}
                                </td>
                                <td class="text-end">
                                    @php
                                        $cost = $b->modal_awal + ($b->cost_kerja ?? 0) + ($b->cost_op ?? 0);
                                        $susut = (1 - $b->gr_akhir / $b->gr) * 100;

                                    @endphp
                                    {{ empty($b->gr_akhir) ? 0 : number_format($cost / $b->gr_akhir, 0) }}
                                </td>
                                <td>
                                    @if ($b->pcs == $b->pcs_akhir)
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        @if ($susut < 50)
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-danger">Belum Selesai</span>
                                        @endif
                                    @endif
                                </td>
                                <td><input type="checkbox" name="" id=""></td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
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
                $(document).ready(function() {
                    $(document).on("change", ".bulan_op", function(e) {
                        e.preventDefault();
                        var id_oprasional = $(this).val();


                        $.ajax({
                            type: "get",
                            url: "{{ route('summary.get_operasional') }}",
                            data: {
                                id_oprasional: id_oprasional
                            },
                            success: function(response) {
                                $('#cost_opr').html(response);
                            }
                        });

                    });
                });
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
