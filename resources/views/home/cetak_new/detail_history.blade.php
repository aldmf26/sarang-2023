<div class="row">
    <div class="col-lg-4">
        <table class="table">
            <tr>
                <th width="1">Pgws</th>
                <th width="1">:</th>
                <th>
                    {{ auth()->user()->name }}
                </th>
            </tr>
            <tr>
                <th width="1">Nama</th>
                <th width="1">:</th>
                <th>{{ $detail[0]->nm_anak }}</th>
            </tr>
            <tr>
                <th width="1">Kelas</th>
                <th width="1">:</th>
                <th>{{ $detail[0]->id_kelas }}</th>
            </tr>
            {{-- <tr>
                <th width="100">Hari Masuk</th>
                <th width="1">:</th>
                <th>{{ $ttl_hari }}</th>
            </tr> --}}
        </table>
    </div>
    <div class="col-lg-8">

        <a target="_blank"
            href="{{ route('cetaknew.print_slipgaji', [
                'id_anak' => $id_anak,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'ttl_hari' => $ttl_hari,
            ]) }}"
            class="btn btn-sm btn-primary float-end align-middle"><i class="fas fa-print"></i> Print</a>
    </div>
</div>
<br>
<div class="row">

    <div class="col">
        <table class="table table-bordered table-hover" id="tblDetail">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Ket</th>
                    <th class="dhead text-end">pcs awal</th>
                    <th class="dhead text-end">gr awal</th>
                    <th class="dhead text-end">pcs akhir</th>
                    <th class="dhead text-end">gr akhir</th>
                    <th class="dhead text-end">sst%</th>
                    <th class="dhead text-end">Ttl Rp</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $categories = [
                        'cabut' => 'Cbt',
                        'sortir' => 'Sortir',
                        'eo' => 'Eo',
                        'detail' => 'Ctk',
                        'dll' => 'Dll',
                        'denda' => 'Denda',
                    ];
                @endphp

                @foreach ($categories as $category => $label)
                    @foreach ($category as $c)
                        @php
                            $no++;
                            $isDenda = $label == 'Denda';
                        @endphp
                        <tr @if ($isDenda) class="text-danger" @endif>
                            <td>{{ $no }}</td>
                            <td>{{ tglFormat($c->tgl) }}</td>
                            <td>{{ $category != 'dll' && $category != 'denda' ? $c->no_box : '-' }}</td>
                            <td>{{ $label }}</td>

                            @if ($category == 'eo')
                                <td class="text-end">-</td>
                            @else
                                <td class="text-end">{{ $c->pcs_awal ?? '-' }}</td>
                            @endif

                            <td class="text-end">{{ $c->gr_awal ?? '-' }}</td>

                            @if ($category == 'eo')
                                <td class="text-end">-</td>
                            @else
                                <td class="text-end">{{ $c->pcs_akhir ?? '-' }}</td>
                            @endif

                            <td class="text-end">{{ $c->gr_akhir ?? '-' }}</td>

                            <td class="text-end">
                                @if ($category == 'detail')
                                    {{ empty($c->gr_awal_ctk) ? 0 : number_format((1 - $c->gr_akhir / $c->gr_awal_ctk) * 100, 1) }}%
                                @else
                                    {{ number_format($c->susut, 1) ?? '-' }}%
                                @endif
                            </td>

                            <td class="text-end">
                                @if ($isDenda)
                                    {{ number_format($c->denda) }}
                                @else
                                    {{ number_format($category == 'detail' ? $c->pcs_akhir * $c->rp_satuan : $c->ttl_rp) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th class="dhead text-center" colspan="4">TOTAL</th>
                    <th class="text-end dhead">{{ number_format($ttlpcs_awal, 0) }}</th>
                    <th class="text-end dhead">{{ number_format($ttlgr_awal, 0) }}</th>
                    <th class="text-end dhead">{{ number_format($ttlpcs_akhir, 0) }}</th>
                    <th class="text-end dhead">{{ number_format($ttlgr_akhir, 0) }}</th>
                    <th class="text-end dhead"></th>
                    <th class="text-end dhead">{{ number_format($ttlttl_rp, 0) }}</th>
                </tr>
                <tr>
                    <th class="dhead text-end" colspan="9">Rata2</th>
                    <th class="text-end dhead">
                        {{-- {{ emptty($ttl_hari) ? 0 : number_format($ttlttl_rp / $ttl_hari, 0) }} --}}
                    </th>
                </tr>
                {{-- <tr>
                    <th class="dhead text-end" colspan="4">Hari Masuk</th>
                    <th class="dhead">{{ number_format($ttlttl_rp / $ttl_hari,0) }}</th>
                </tr> --}}
            </tfoot>
        </table>
    </div>
</div>
