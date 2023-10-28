<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">
                <a href="{{ route('cabut.export_rekap', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
                    class="float-end btn btn-sm icon icon-left btn-primary me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <x-theme.btn_filter />
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.cetak.nav')

        </div>

    </x-slot>

    <x-slot name="cardBody">
        <style>
            .badge {
                cursor: pointer;
            }
        </style>

        <section class="row">
            <div class="col-lg-4 mb-2">
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

                <table style="border:1px solid #97a1c3" class="table table-bordered" id="tablealdi"
                    x-data="{
                        openRows: [],
                    }">
                    <thead>
                        <tr>
                            <th class="dhead" width="120">Pengawas </th>
                            <th class="dhead text-end" width="50">No Box</th>
                            <th class="dhead text-end">Pcs Awal Bk <br> ({{ number_format($ttlPcsBk, 0) }})</th>
                            <th class="dhead text-end">Gr Awal Bk <br> ({{ number_format($ttlGrBk, 0) }})</th>
                            <th class="dhead text-end">Pcs Awal Kerja <br> ({{ number_format($ttlPcsAwal, 0) }})</th>
                            <th class="dhead text-end">Gr Awal Kerja <br> ({{ number_format($ttlGrAwal, 0) }})</th>
                            <th class="dhead text-end" width="80">Total Rupiah <br> (Rp
                                {{ number_format($ttlRp, 0) }})</th>
                            <th class="dhead text-end">Pcs Sisa Bk</th>
                            <th class="dhead text-end">Gr Sisa Bk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabutGroup as $i => $d)
                            <tr>
                                <th>{{ $d->pengawas }} <span class="badge bg-primary float-end"
                                        x-on:click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">Buka
                                        <i class="fas fa-caret-down"></i></span></th>
                                <th class="text-end">Ttl Box : {{ number_format($d->ttl_box, 0) }}</th>
                                <th class="text-end">{{ number_format($d->pcs_bk, 0) }}</th>
                                <th class="text-end">{{ number_format($d->gr_bk, 0) }}</th>
                                <th class="text-end">{{ number_format($d->pcs_awal, 0) }}</th>
                                <th class="text-end">{{ number_format($d->gr_awal, 0) }}</th>
                                <th class="text-end">{{ number_format($d->ttl_rp, 0) }}</th>
                                <th class="text-end">{{ number_format($d->pcs_bk - $d->pcs_awal, 0) }}</th>
                                <th class="text-end">{{ number_format($d->gr_bk - $d->gr_awal, 0) }}</th>
                            </tr>
                            @php
                                $id = $d->id_pengawas;
                                $query = DB::select("SELECT max(b.name) as pengawas, max(a.tgl_terima) as tgl, a.no_box, 
                                            SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
                                            SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, c.pcs_awal as pcs_bk, c.gr_awal as gr_bk,
                                            sum(a.pcs_hcr) as pcs_hcr, sum(a.eot) as eot, sum(a.rupiah) as rupiah,sum(a.ttl_rp) as ttl_rp, sum(a.gr_flx) as gr_flx
                                            FROM cabut as a
                                            left join users as b on b.id = a.id_pengawas
                                            left JOIN bk as c on c.no_box = a.no_box 
                                            WHERE  a.id_pengawas = '$id'
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
                            <td align="right">{{ $x->pcs_bk }}</td>
                            <td align="right">{{ $x->gr_bk }}</td>
                            <td align="right">{{ $x->pcs_awal }}</td>
                            <td align="right">{{ $x->gr_awal }}</td>
                            <td align="right">{{ number_format($x->ttl_rp, 0) }}</td>
                            <td align="right">{{ $x->pcs_bk - $x->pcs_awal }}</td>
                            <td align="right">{{ $x->gr_bk - $x->gr_awal }}</td>
                        </tr>
                    </tbody>
                    @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>

        </section>

        @section('scripts')
            <script>
                pencarian('pencarian', 'tablealdi')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
