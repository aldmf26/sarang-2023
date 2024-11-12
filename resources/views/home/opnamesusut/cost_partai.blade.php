<x-theme.app title="{{ $title }}" table="Y" sizeCard="11" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <select name="" id="select2" class="form-control pilih_partai">
                            <option value="">Pilih Partai</option>
                            @foreach ($partai as $p)
                                <option value="{{ $p->nm_partai }}">{{ $p->nm_partai }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12 mt-4">
                        <div id="loadHalaman"></div>
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
                        beforeSend: function() {
                            $('#cost_opr').html("loading...");
                        },
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
                    $('.pilih_partai').change(function(e) {
                        e.preventDefault();
                        var partai = $(this).val();
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.getCostpartai') }}",
                            data: {
                                partai: partai
                            },
                            beforeSend: function() {
                                $('#loadHalaman').html("loading...");
                            },
                            success: function(response) {
                                $('#loadHalaman').html(response);

                            }
                        });

                    });
                });
            </script>
        @endsection

    </x-slot>
</x-theme.app>
