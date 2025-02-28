<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <div class="row">
            <div class="col-6">
                <h6>Opname sebelumnya</h6>
                <table class="table table-bordered border-dark">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Divisi</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($previousOpnameData as $d)
                            <tr>
                                <td>{{ $d->kategori }}</td>
                                <td>{{ number_format($d->pcs, 0) }}</td>
                                <td>{{ number_format($d->gr, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <h6>Opname sekarang</h6>
                <table class="table table-bordered border-dark">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Divisi</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $selanjutnya = [
                                'grading', 'wip', 'qc', 'pengiriman'
                            ];
                        @endphp
                        @foreach ($selanjutnya as $d)
                            <tr>
                                <td>{{ $d }}</td>
                                <td>
                                    <input type="text" name="pcs_{{ $d }}" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="text" name="gr_{{ $d }}" class="form-control form-control-sm">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>
</x-theme.app>
