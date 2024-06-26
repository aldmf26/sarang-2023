<x-theme.app sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.button_nav')


    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-10 mb-3">
                @include('home.gradingbj.nav')
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grade</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gram</th>
                            <th class="text-end">Ttl Rp</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudangbj as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ $g->pcs_akhir - $g->pcs_ambil }}</td>
                                <td class="text-end">{{ $g->gr_akhir - $g->gr_ambil }}</td>
                                <td class="text-end">
                                    {{ number_format($g->ttl_rp + $g->ttl_rp_sortir, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>
    </x-slot>
</x-theme.app>
