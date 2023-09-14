<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <h6 class="float-start mt-1">{{ $title }}</h6>
        <a href="" class="btn btn-success float-end"><i class="fas fa-file-excel"></i> Export</a>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pengawas</th>
                        <th>Nama Anak</th>
                        <th>Kelas</th>
                        <th>Masuk</th>
                        <th class="text-end">Cabut</th>
                        <th class="text-end">Cabut Spesial</th>
                        <th class="text-end">EO</th>
                        <th class="text-end">Cetak</th>
                        <th class="text-end">Sortir</th>
                        <th class="text-end">Harian Dll</th>
                        <th class="text-end">Denda</th>
                        <th class="text-end">Total Gaji</th>
                        <th class="text-end">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($anak as $no => $a)
                    <tr>
                        <td>{{$no+1}}</td>
                        <td>{{$a->name}}</td>
                        <td>{{$a->nama}}</td>
                        <td>{{$a->id_kelas}}</td>
                        <td>{{$a->absen}}</td>
                        <td align="right">Rp. {{number_format($a->rupiah - $a->d_susut - $a->d_hcr +
                            $a->eot_lebih,0)}}</td>
                        <td align="right">Rp. {{number_format($a->rp_spesial,0)}}</td>
                        <td align="right">Rp. {{number_format($a->rp_eo,0)}}</td>
                        <td align="right">Rp. {{number_format($a->rp_pcs_cetak - $a->d_cetak)}}</td>
                        <td align="right">Rp. {{number_format($a->rp_sortir,0)}}</td>
                        <td align="right">Rp. {{number_format($a->rp_dll,0)}}</td>
                        <td align="right">Rp. 0</td>
                        @php
                        $total = ($a->rupiah - $a->d_susut - $a->d_hcr + $a->eot_lebih) + $a->rp_spesial + $a->rp_eo +
                        ($a->rp_pcs_cetak - $a->d_cetak) + $a->rp_sortir + $a->rp_dll
                        @endphp
                        <td align="right">Rp. {{number_format($total,0)}}</td>
                        <td align="right">Rp. {{number_format($total / $a->absen,0)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>


        @section('scripts')
        <script>
            $(".select3").select2()

            
        </script>
        @endsection
    </x-slot>
</x-theme.app>