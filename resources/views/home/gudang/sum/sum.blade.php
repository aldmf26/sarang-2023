<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" >
    <x-slot name="cardHeader">
        <h6>{{ $title }}</h6>
        @include('home.gudang.sum.nav')
    </x-slot>
    <x-slot name="cardBody">
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Keterangan</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Ttl Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Bk dari Sinta</td>
                            <td align="right">{{ number_format(119874,0) }}</td>
                            <td align="right">{{ number_format(969673,0) }}</td>
                            <td align="right">{{ number_format(7848039841,0) }}</td>
                        </tr>
                        @for ($i = 0; $i < 3; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dhead">Total</th>
                            <th class="dhead text-end">{{ number_format(119874,0) }}</th>
                            <th class="dhead text-end">{{ number_format(969673,0) }}</th>
                            <th class="dhead text-end">{{ number_format(7848039841,0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Keterangan</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Ttl Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cabut awal</td>
                            <td align="right">{{ number_format(119874,0) }}</td>
                            <td align="right">{{ number_format(969673,0) }}</td>
                            <td align="right">{{ number_format(7848039841,0) }}</td>
                        </tr>
                        <tr>
                            <td>Cabut akhir</td>
                            <td align="right">{{ number_format(119874,0) }}</td>
                            <td align="right">{{ number_format(969673,0) }}</td>
                            <td align="right">{{ number_format(7848039841,0) }}</td>
                        </tr>
                        <tr>
                            <td>Sedang proses</td>
                            <td align="right">{{ number_format(119874,0) }}</td>
                            <td align="right">{{ number_format(969673,0) }}</td>
                            <td align="right">{{ number_format(7848039841,0) }}</td>
                        </tr>
                        <tr>
                            <td>Sisa pgws</td>
                            <td align="right">{{ number_format(119874,0) }}</td>
                            <td align="right">{{ number_format(969673,0) }}</td>
                            <td align="right">{{ number_format(7848039841,0) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dhead">Total</th>
                            <th class="dhead text-end">{{ number_format(119874,0) }}</th>
                            <th class="dhead text-end">{{ number_format(969673,0) }}</th>
                            <th class="dhead text-end">{{ number_format(7848039841,0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <table class="table">
            <tr>
                <td></td>
            </tr>
        </table>
    </x-slot>   
    @section('scripts')
        <script>
            
        </script>
    @endsection
</x-theme.app>
