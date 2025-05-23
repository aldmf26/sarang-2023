<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <div class="col-lg-6">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Box Grading</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gr Awal</th>
                            <th class="text-end">Gr Akhir</th>
                            <th class="text-end">Susut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grading as $g)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $g->box_pengiriman }}</td>
                                <td class="text-end">{{ $g->pcs_awal }}</td>
                                <td class="text-end">{{ $g->gr_awal }}</td>
                                <td class="text-end">{{ $g->gr_akhir }}</td>
                                <td class="text-end">{{ $g->gr_awal - $g->gr_akhir }}</td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>
        </section>








        @section('scripts')
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
