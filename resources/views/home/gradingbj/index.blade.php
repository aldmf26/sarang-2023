<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div></div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <section>
            <table id="table" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="dhead">#</th>
                        <th class="dhead">Tanggal</th>
                        <th class="dhead">No Invoice</th>
                        <th class="dhead">No Box</th>
                        <th class="dhead">Pemberi</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulir as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ tanggal($d->tanggal) }}</td>
                            <td>{{ $d->no_invoice }}</td>
                            <td>{{ $d->no_box }}</td>
                            <td>{{ $d->pemberi }}</td>
                            <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                            <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                            <td class="text-center">
                                <a href="{{ route('gradingbj.grading', $d->no_box) }}" class="bg-primary text-white badge">Grading</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </x-slot>
</x-theme.app>
