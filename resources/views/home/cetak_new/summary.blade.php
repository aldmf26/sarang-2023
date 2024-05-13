<x-theme.app title="{{ $title }} " table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}
                {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}
                <span class="text-warning" style="font-size: 12px"><em>jika data tidak ada silahkan view dulu
                        !</em></span>
            </h6>
            <div>
                @include('home.cabut.view_bulandibayar')
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <div class="row">
            <div class="col">
                <table id="tblHistory" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Pgws</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead text-center">Hari Kerja</th>
                            <th class="dhead text-end">Rp Gaji</th>
                            <th class="dhead text-end">Rata-rata</th>
                            <th class="dhead text-center">Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $i => $d)
                        @php
                            $ttl_hari = $d->ttl_hari;
                            $ttl_rp = $d->ttl_rp + $d->ttl_rp_cabut + $d->ttl_rp_sortir;
                            $rata2 = $ttl_rp / $ttl_hari;
                            $target = 90000;
                        @endphp
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $d->pgws }}</td>
                            <td>{{ $d->nama }}</td>
                            <td align="right">{{ $ttl_hari }}</td>
                            <td align="right">{{ number_format($ttl_rp,0) }}</td>
                            <td align="right">{{ number_format($rata2,0) }}</td>
                            <td align="center">{{ $rata2 < $target ? 'Tidak' : '' }} Capai</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

     
    </x-slot>
</x-theme.app>
