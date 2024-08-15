<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Summary</h6>
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="responsive" style="overflow-x: auto;">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="judul-sidebar first-column_atas">Akhir</th>
                            <th class="judul-sidebar second-column_atas"></th>
                            <th>A</th>
                            <th>awal</th>
                            <th></th>
                            <th>B1</th>
                            <th>awal</th>
                            <th></th>
                            <th>B2</th>
                            <th>akhir</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="nowrap">C (sisa)</th>
                            <th>awal</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th class="judul-sidebar first-column_atas"></th>
                            <th class="judul-sidebar second-column_atas"></th>

                            <th>pcs</th>
                            <th>gr</th>
                            <th>rp</th>

                            <th>pcs</th>
                            <th>gr</th>
                            <th>rp</th>

                            <th>pcs</th>
                            <th>gr</th>
                            <th>rp</th>
                            <th class="nowrap">cost kerja</th>
                            <th class="nowrap">cost operasional</th>
                            <th class="nowrap">cost dll cu denda</th>

                            <th>pcs</th>
                            <th>gr</th>
                            <th>rp</th>
                        </tr>
                    </thead>

                    @foreach ($cabut as $j)
                        <tr>
                            <td class="judul-sidebar first-column">{{ $j[0] }}</td>
                            <td class="judul-sidebar second-column">{{ $j[2] }}</td>
                            <td>0</td>
                            <td>0</td>
                            <td>11,000,000,000</td>

                            <td>0</td>
                            <td>0</td>
                            <td>11,000,000,000</td>

                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>11,000,000,000</td>

                            <td>0</td>
                            <td>0</td>
                            <td>11,000,000,000</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="17">
                            <hr style="border: 1px solid black">
                        </th>
                    </tr>
                    @foreach ($cetak as $j)
                        <tr>
                            <th>{{ $j[0] }}</th>
                            <th>{{ $j[2] }}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="17">
                            <hr style="border: 1px solid black">
                        </th>
                    </tr>
                    @foreach ($sortir as $j)
                        <tr>
                            <th>{{ $j[0] }}</th>
                            <th>{{ $j[2] }}</th>
                        </tr>
                    @endforeach


                </table>
            </div>


        </section>
    </x-slot>
</x-theme.app>
