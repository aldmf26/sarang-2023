<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <style>
            
        </style>
        <div class="row">
            @foreach ($cards as $d)
            @php
                $pcs = $d['body']['pcs'] ?? 0;
                $gr = $d['body']['gr'] ?? 0;
                $ttl_rp = $d['body']['ttl_rp'] ?? 0;
            @endphp
                <div class="col-lg-3 ">
                    <div class="card cardHover pointer text-center border border-secondary">
                       
                        <div class="card-body">
                            <h5>{{ $d['no'] }}</h5>
                            <h6>{{ strtoupper($d['title']) }}</h6>
                            <table class="table text-end">
                                <tr>
                                    <th class="text-start">Pcs</th>
                                    <th>:</th>
                                    <th>{{ number_format($pcs,0) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-start">Gr</th>
                                    <th>:</th>
                                    <th>{{ number_format($gr,0) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-start">Ttl Rp</th>
                                    <th>:</th>
                                    <th>{{ number_format($ttl_rp,0) }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>


        @section('scripts')
            <script></script>
        @endsection
    </x-slot>
</x-theme.app>
