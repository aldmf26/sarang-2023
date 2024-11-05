<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2" class="dhead">Nama Pengawas</th>
                            <th colspan="2" class="dhead text-center">Cabut proses</th>
                            <th colspan="2" class="dhead text-center">Cabut sisa pengawas</th>
                            <th colspan="2" class="dhead text-center">Cabut selesai siap cetak</th>
                            <th colspan="2" class="dhead text-center">Total</th>
                            <th colspan="5" class="dhead text-center">Susut</th>
                        </tr>
                        <tr>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">nama posisi</th>
                            <th class="dhead text-end">kelas</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">%</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $nama_terisi = [];
                        @endphp
                        @foreach ($pgws_cabut as $p)
                            <tr>
                                @if ()
                                    
                                @endif
                                <td>{{ $p->name }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                                <td>Cabut {{ $p->name }}</td>
                                <td>{{ $p->tipe }}</td>
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
