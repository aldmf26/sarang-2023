<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.button_nav')

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8 mb-3">
                @include('home.gradingbj.nav', ['name' => 'index'])
            </div>
            <div class="col-lg-12">
                <table class="table" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tgl</th>
                            <th>Partai</th>
                            <th>No Grading</th>
                            <th class="text-end">Ttl Pcs</th>
                            <th class="text-end">Ttl Gr</th>
                            <th class="text-end">Ttl Rp</th>
                            <th width="20%" class="text-center">Grading</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td>{{ $d->partai }}</td>
                                <td>{{ "GRDBJ-$d->no_grading" }}</td>
                                <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                <td align="center">
                                    <span class="btn btn-sm btn-primary detail" no_grading="{{ $d->no_grading }}"><i
                                            class="fas fa-eye"></i></span>
                                    @php
                                        $cekGrading = DB::table('pengiriman_list_gradingbj')
                                            ->where('no_grading', $d->no_grading)
                                            ->first();
                                    @endphp
                                    @if (!$cekGrading)
                                        <span class="btn btn-sm btn-primary grading"
                                            no_grading="{{ $d->no_grading }}"><i
                                                class="fas fa-hourglass-half "></i></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </section>

        
    </x-slot>
</x-theme.app>
