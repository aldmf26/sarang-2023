<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-3">
                <div>
                    <h6> Total susut aktual</h6>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Ket</th>
                            <th class="dhead text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cabut</td>

                            <td></td>
                        </tr>
                        <tr>
                            <td>Cetak</td>

                            <td></td>
                        </tr>
                        <tr>
                            <td>Sortir</td>

                            <td></td>
                        </tr>
                        <tr>
                            <td>Eo</td>

                            <td></td>
                        </tr>
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
