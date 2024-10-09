<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-8">
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Tgl Kirim</th>
                            <th class="dhead">No Packinglist</th>
                            <th class="dhead">Nama Packing List</th>
                            <th class="dhead">Tujuan</th>
                            <th class="dhead text-end">Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Gr + Kadar</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($query as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td>PI {{ $d->no_nota }}</td>
                                <td>{{ ucwords($d->nm_packing) }}</td>
                                <td>{{ strtoupper($d->tujuan) }}</td>
                                <td align="center">{{ $d->ttl_box }}</td>
                                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                <td align="right"><a href="{{ route('detail.list_pengiriman') }}">{{ number_format($d->gr, 0) }}</a></td>
                                <td align="right"><a href="{{ route('detail.list_pengiriman') }}">{{ number_format($d->gr_naik, 0) }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
