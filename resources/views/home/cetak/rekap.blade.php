<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">
                <a href="{{ route('cetak.export_rekap', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
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

        <section class="row">
            <table class="table table-bordered table-hover table-striped" id="table">
                <thead>
                    <tr>
                        <th class="dhead">Bulan</th>
                        <th class="dhead">Pengawas</th>
                        <th class="dhead">No Box</th>
                        <th class="dhead text-end">Pcs Akhir Cabut</th>
                        <th class="dhead text-end">Gr Akhir Cabut</th>
                        <th class="dhead text-end">Pcs Awal Cetak</th>
                        <th class="dhead text-end">Gr Awal Cetak</th>
                        @php
                            $ttl = 0;
                            foreach ($datas as $d) {
                                $susut = empty($d->gr_akhir) ? '0' : (1 - $d->gr_akhir / ($d->gr_awal - $d->gr_tidak_ctk)) * 100;
                                $denda = round($susut, 0) * 50000;
                                $ttl += $d->rp_pcs * $d->pcs_awal - $denda;
                            }
                        @endphp
                        <th class="dhead text-end">Total Rupiah ({{ number_format($ttl, 0) }})</th>
                        <th class="dhead text-end">Pcs Sisa Bk</th>
                        <th class="dhead text-end">Gr Sisa Bk</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $n => $d)
                        <tr>
                            <td>{{ date('M Y', strtotime($d->tgl)) }}</td>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->no_box }}</td>
                            <td class="text-end">{{ $d->cabut_pcs_akhir }}</td>
                            <td class="text-end">{{ $d->cabut_gr_akhir }}</td>
                            <td class="text-end">{{ $d->pcs_awal }}</td>
                            <td class="text-end">{{ $d->gr_awal }}</td>
                            @php
                                $susut = empty($d->gr_akhir) ? '0' : (1 - $d->gr_akhir / ($d->gr_awal - $d->gr_tidak_ctk)) * 100;
                                $denda = round($susut, 0) * 50000;
                            @endphp
                            <td align="right">Rp. {{ number_format($d->rp_pcs * $d->pcs_awal - $denda, 0) }}</td>
                            <td class="text-end">{{ $d->pcs_awal - $d->cabut_pcs_akhir }}</td>
                            <td class="text-end">{{ $d->gr_awal - $d->cabut_gr_akhir }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    </x-slot>

</x-theme.app>
