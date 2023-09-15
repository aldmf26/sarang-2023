<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">
                <a href="{{ route('cabut.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
                    class="float-end btn btn-sm icon icon-left btn-primary me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <x-theme.btn_filter />
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            <ul class="nav nav-pills float-start">
                @php
                $rot = request()
                ->route()
                ->getName();
                @endphp

                <li class="nav-item">
                    <a class="nav-link {{ $rot == 'cabut.rekap' ? 'active' : '' }}" aria-current="page"
                        href="{{ route('cabut.rekap') }}">Cabut</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'cabutSpesial.rekap' ? 'active' : '' }}"
                        href="{{ route('cabutSpesial.rekap') }}">Cabut Spesial</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'eo.rekap' ? 'active' : '' }}" href="{{ route('eo.rekap') }}">EO</a>

                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'cetak.rekap' ? 'active' : '' }}"
                        href="{{ route('cetak.rekap') }}">Cetak</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'sortir.rekap' ? 'active' : '' }}"
                        href="{{ route('sortir.rekap') }}">Sortir</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'hariandll.rekap' ? 'active' : '' }}"
                        href="{{ route('hariandll.rekap') }}">DLL</a>
                </li>
            </ul>
        </div>

    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table  table-bordered" id="table">
                <thead>
                    <tr>
                        <th class="dhead">Bulan</th>
                        <th class="dhead">Pengawas</th>
                        <th class="dhead ">No Box</th>
                        <th class="dhead text-end">Pcs Awal Bk</th>
                        <th class="dhead text-end">Gr Awal Bk</th>
                        <th class="dhead text-end">Pcs Awal Kerja</th>
                        <th class="dhead text-end">Gr Awal Kerja</th>
                        <th class="dhead text-end">Total Rupiah</th>
                        <th class="dhead text-end">Pcs Sisa Bk</th>
                        <th class="dhead text-end">Gr Sisa Bk</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabut as $c)
                    <tr>
                        <td>{{date("M y",strtotime($c->tgl)) }}</td>
                        <td>{{$c->pengawas}}</td>
                        <td>{{$c->no_box}}</td>
                        <td align="right">{{$c->pcs_bk}}</td>
                        <td align="right">{{$c->gr_bk}}</td>
                        <td align="right">{{$c->pcs_awal}}</td>
                        <td align="right">{{$c->gr_awal}}</td>
                        @php
                        $susut = empty($c->gr_akhir) ? 0 : (1 - ($c->gr_flx + $c->gr_akhir) / $c->gr_awal) * 100;
                        $denda = empty($c->gr_akhir) ? 0 : ($susut > 23.4 ? ($susut - 23.4) * 0.03 * $c->rupiah : 0);
                        $denda_hcr = $c->pcs_hcr * 5000;
                        $eot_bonus = empty($c->eot) ? 0 : ($c->eot - $c->gr_awal * 0.02 )* 750;
                        @endphp
                        <td align="right">{{ number_format($c->rupiah - $denda - $denda_hcr + $eot_bonus, 0) }}
                        </td>
                        <td align="right">{{$c->pcs_bk - $c->pcs_awal}}</td>
                        <td align="right">{{$c->gr_bk - $c->gr_awal}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    </x-slot>

</x-theme.app>