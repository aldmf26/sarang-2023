<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}
                    {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}
                    <span class="text-warning" style="font-size: 12px"><em>jika data tidak ada silahkan view dulu !</em></span>
                </h6>   
            </div>

            <div class="col-lg-6">
                {{-- <a href="{{ route('sortir.export_rekap', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="float-end btn btn-sm btn-primary me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a> --}}
                @include('home.cabut.btn_export_global')

            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.cetak.nav')
        </div>

    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <div class="col-lg-12 mb-2">
                <table class="float-end">
                    <tbody>
                        <tr>
                            <td>Pencarian :</td>
                            <td><input autofocus type="text" id="pencarian" class="form-control float-end"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
              
                <section class="row">
                    <table style="border:1px solid #97a1c3" class="table table-bordered" id="tblAld2"
                        x-data="{
                            openRows: [],
                        }">
                        <thead>
                            <tr>
                                <th width="180" rowspan="2" class="text-center dhead">Pengawas</th>
                                <th width="85" rowspan="2" class="text-center dhead">No Box</th>
                                <th colspan="2" class="text-center dhead">BK Awal</th>
                                <th colspan="2" class="text-center dhead"> Kerja Awal</th>
                                <th colspan="3" class="text-center dhead"> Kerja Akhir</th>
                                <th width="100" rowspan="2" class="text-center dhead">Ttl Rp <br> (
                                    {{ number_format($ttlRp, 0) }})</th>
                                <th width="2%" class="text-center dhead" colspan="2">BK Sisa</th>
                            </tr>

                            <tr>
                                <th class="dhead text-center">Pcs <br> ({{ number_format($ttlPcsBk, 0) }})</th>
                                <th class="dhead text-center">Gr <br> ({{ number_format($ttlGrBk, 0) }})</th>
                                <th class="dhead text-center">Pcs <br> ({{ number_format($ttlPcsAwal, 0) }})</th>
                                <th class="dhead text-center">Gr <br> ({{ number_format($ttlGrAwal, 0) }})</th>
                                <th class="dhead text-center">Pcs <br> ({{ number_format($ttlPcsAkhir, 0) }})</th>
                                <th class="dhead text-center">Gr <br> ({{ number_format($ttlGrAkhir, 0) }})</th>
                                <th class="dhead text-center">Susut </th>
                                <th class="dhead text-center">Pcs </th>
                                <th class="dhead text-center">Gr</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sortirGroup as $i => $d)
                                <tr>
                                    <th>{{ $d->pengawas }} <span class="badge bg-primary float-end"
                                            x-on:click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">Buka
                                            <i class="fas fa-caret-down"></i></span></th>
                                    <th class="text-end">Ttl Box : {{ number_format($d->ttl_box, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->pcs_bk, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_bk, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->pcs_awal, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_awal, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->pcs_akhir, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_akhir, 0) }}</th>
                                    @php
                                       $susut =  empty($d->gr_awal) ? 0 : (1 - ($d->gr_akhir / $d->gr_awal)) * 100
                                    @endphp
                                    <th class="text-end">{{ number_format($susut, 0) }} %</th>
                                    <th class="text-end">{{ number_format($d->ttl_rp, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->pcs_bk - $d->pcs_awal, 0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_bk - $d->gr_awal, 0) }}</th>
                                </tr>
                                @php
                                    $id = $d->id_pengawas;
                                    $query = DB::select("SELECT max(b.name) as pengawas, max(a.tgl) as tgl, a.no_box, 
                                            SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
                                            SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, c.pcs_bk, c.gr_bk,
                                             sum(a.rp_target) as rp_target,sum(a.ttl_rp) as ttl_rp,sum((1 - a.gr_akhir / a.gr_awal) * 100) as susut
                                            FROM sortir as a
                                            left join users as b on b.id = a.id_pengawas
                                            LEFT JOIN (
                                                SELECT no_box,penerima, sum(pcs_awal) as pcs_bk, sum(gr_awal) as gr_bk FROM bk where selesai = 'T' and kategori LIKE '%sortir%' GROUP BY no_box,penerima
                                            ) as c on c.no_box = a.no_box and c.penerima = a.id_pengawas
                                            WHERE  a.id_pengawas = '$id' AND a.no_box != 9999 AND a.penutup = 'T' AND a.bulan = '$bulan' AND YEAR(a.tgl) = '$tahun'
                                            GROUP by a.no_box");
                                @endphp
                                @foreach ($query as $x)
                        <tbody x-show="openRows.includes({{ $i }})">

                            <tr>
                                <td>{{ $d->pengawas }} <span class="badge bg-primary float-end"
                                        x-on:click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">
                                        <i class="fas fa-caret-up"></i></span></td>
                                <td align="right"><a class="detail" target="_blank"
                                        href="{{ route('dashboard.detail', $x->no_box) }}">{{ number_format($x->no_box, 0) }}
                                        <i class="me-2 fas fa-eye"></i></a></td>
                                <td align="right">{{ number_format($x->pcs_bk, 0) }}</td>
                                <td align="right">{{ number_format($x->gr_bk, 0) }}</td>
                                <td align="right">{{ number_format($x->pcs_awal, 0) }}</td>
                                <td align="right">{{ number_format($x->gr_awal, 0) }}</td>
                                <td align="right">{{ number_format($x->pcs_akhir, 0) }}</td>
                                <td align="right">{{ number_format($x->gr_akhir, 0) }}</td>
                                @php
                                $susut =  empty($x->gr_awal) ? 0 : (1 - ($x->gr_akhir / $x->gr_awal)) * 100
                             @endphp
                                <td align="right">{{ number_format($susut, 0) }} %</td>

                                <td align="right">{{ number_format($x->ttl_rp, 0) }}</td>
                                <td align="right">{{ number_format($x->pcs_bk - $x->pcs_awal, 0) }}</td>
                                <td align="right">{{ number_format($x->gr_bk - $x->gr_awal, 0) }}</td>
                            </tr>
                        </tbody>
                        @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </section>
            </div>

        </section>
        @section('scripts')
            <script>
                pencarian('pencarian', 'tblAld2')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
