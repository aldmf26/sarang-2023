<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
       
        <section class="row">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr>
                        <th class="dhead text-center">No</th>
                        <th class="dhead">Partai</th>
                        <th class="dhead">Box Grading</th>
                        <th class="dhead text-center">Grade</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="bg-warning text-center text-white">Grade 2</th>
                        <th class="bg-warning text-white text-end">Pcs 2</th>
                        <th class="bg-warning text-white text-end">Gr 2</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cek as $i =>  $d)
                        <tr>
                            <td align="center">{{ $i+1 }}</td>
                            <td>{{ $d->nm_partai }}</td>
                            <td>{{ $d->box_grading }}</td>
                            <td align="center">{{ $d->grade }}</td>
                            <td align="right">{{ $d->pcs }}</td>
                            <td align="right">{{ $d->gr }}</td>
                            <td align="center">{{ $d->grade2 }}</td>
                            <td align="right">{{ $d->pcs2 }}</td>
                            <td align="right">{{ $d->gr2 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    
    </x-slot>
</x-theme.app>
