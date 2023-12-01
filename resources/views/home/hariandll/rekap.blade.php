<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}</h6>
            </div>

            <div class="col-lg-6">
                <a href="{{ route('hariandll.export_rekap', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="float-end btn btn-sm btn-primary me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                @include('home.cabut.btn_export_global')

            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.cetak.nav')

        </div>

    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table  table-bordered" id="table">
                <thead>
                    <tr>
                        <th class="dhead ">Pgws</th>

                        @php
                            $ttl = 0;
                            foreach ($datas as $d) {
                                $ttl += $d->total_rupiah;
                            }
                        @endphp

                        <th class="dhead text-end">Rupiah ({{ number_format($ttl, 0) }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td align="right">{{ number_format($c->total_rupiah, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    </x-slot>

</x-theme.app>
