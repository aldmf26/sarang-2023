<x-theme.app title="{{ $title }} " table="Y" sizeCard="6" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <div class="col-lg-4">
                <h6 class="">{{ $title }}</h6>
            </div>
            <div class="col-lg-8">
                <x-theme.btn_filter />
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="nanda">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Nama Pemberi</th>
                        <th>Nama Penerima</th>
                        <th class="text-end">Pcs</th>
                        <th class="text-end">Gr</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulir as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td><a href="{{ route('gudangsarang.print_formulir', ['no_invoice' => $d->no_invoice]) }}"
                                    target="_blank">
                                    {{ $d->no_invoice }}</a></td>
                            <td>{{ $d->pemberi }}</td>
                            <td>{{ $d->penerima }}</td>
                            <td class="text-end">{{ $d->pcs }}</td>
                            <td class="text-end">{{ $d->gr }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </section>
    </x-slot>

</x-theme.app>
