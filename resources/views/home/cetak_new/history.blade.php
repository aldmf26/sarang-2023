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
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead text-end">Ttl Hari</th>
                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Gr Awal</th>
                            <th class="dhead text-end">Pcs Akhir</th>
                            <th class="dhead text-end">Gr Akhir</th>
                            <th class="dhead text-end">Ttl Rp</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $i => $d)
                            
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ strtoupper($d->nama) }} / {{ $d->kelas }}</td>
                            <td align="right">{{ $d->ttl_hari }}</td>
                            <td align="right">{{ number_format($d->pcs_awal,0) }}</td>
                            <td align="right">{{ number_format($d->gr_awal,0) }}</td>
                            <td align="right">{{ number_format($d->pcs_akhir,0) }}</td>
                            <td align="right">{{ number_format($d->gr_akhir,0) }}</td>
                            <td align="right">{{ number_format($d->ttl_rp,0) }}</td>
                            <td><button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button></td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>
</x-theme.app>
