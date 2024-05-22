<div class="row">
    <div class="col">
        <table class="table table-bordered table-hover" id="tblDetail">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Tanggal</th>
                    <th class="dhead">Nama</th>
                    <th class="dhead">Ket 2</th>
                    <th class="dhead text-end">pcs awal</th>
                    <th class="dhead text-end">gr awal</th>
                    <th width="130px" class="dhead text-end">pcs akhir</th>
                    <th width="130px" class="dhead text-end">gr akhir</th>
                    <th class="dhead text-end">sst%</th>
                    <th class="dhead text-end">Total Rp</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($cabut as $c)
                @php
                    $no++;
                @endphp
                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ $c->no_box }}</td>
                        <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                        <td>{{ $c->nm_anak }}</td>
                        <td>Cbt</td>
                        <td class="text-end">{{ $c->pcs_awal }}</td>
                        <td class="text-end">{{ $c->gr_awal }}</td>

                        <td class="text-end">
                            {{ $c->pcs_akhir }}
                        </td>
                        <td class="text-end">
                            {{ $c->gr_akhir }}
                        </td>
                        <td class="text-end">
                            {{ number_format($c->susut, 1) }}%
                        </td>
                        <td class="text-end">{{ number_format($c->ttl_rp) }}</td>

                    </tr>
                @endforeach
                @foreach ($sortir as $c)
                @php
                    $no++;
                @endphp
                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ $c->no_box }}</td>
                        <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                        <td>{{ $c->nm_anak }}</td>
                        <td>Sortir</td>
                        <td class="text-end">{{ $c->pcs_awal }}</td>
                        <td class="text-end">{{ $c->gr_awal }}</td>

                        <td class="text-end">
                            {{ $c->pcs_akhir }}
                        </td>
                        <td class="text-end">
                            {{ $c->gr_akhir }}
                        </td>
                        <td class="text-end">
                            {{ number_format($c->susut, 1) }}%
                        </td>
                        <td class="text-end">{{ number_format($c->ttl_rp) }}</td>

                    </tr>
                @endforeach
                @foreach ($eo as $c)
                @php
                    $no++;
                @endphp
                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ $c->no_box }}</td>
                        <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                        <td>{{ $c->nm_anak }}</td>
                        <td>Eo</td>
                        <td class="text-end">-</td>
                        <td class="text-end">{{ $c->gr_awal }}</td>

                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">
                            {{ $c->gr_akhir }}
                        </td>
                        <td class="text-end">
                            {{ number_format($c->susut, 1) }}%
                        </td>
                        <td class="text-end">{{ number_format($c->ttl_rp) }}</td>

                    </tr>
                @endforeach
                @foreach ($detail as $c)
                @php
                    $no++;
                @endphp
                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ $c->no_box }}</td>
                        <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                        <td>{{ $c->nm_anak }}</td>
                        <td>Ctk</td>
                        <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
                        <td class="text-end">{{ $c->gr_awal_ctk }}</td>

                        <td class="text-end">
                            {{ $c->pcs_akhir }}
                        </td>
                        <td class="text-end">
                            {{ $c->gr_akhir }}
                        </td>
                        <td class="text-end">
                            {{ empty($c->gr_akhir) ? 0 : number_format((1 - $c->gr_akhir / $c->gr_awal_ctk) * 100, 1) }}%
                        </td>
                        <td class="text-end">{{ number_format($c->pcs_akhir * $c->rp_satuan) }}</td>
                    </tr>
                @endforeach
                @foreach ($dll as $c)
                @php
                    $no++;
                @endphp
                    <tr>
                        <td>{{ $no }}</td>
                        <td>-</td>
                        <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                        <td>{{ $c->nm_anak }}</td>
                        <td>Dll</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>

                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">{{ number_format($c->ttl_rp) }}</td>
                    </tr>
                @endforeach
                @foreach ($denda as $c)
                @php
                    $no++;
                @endphp
                    <tr>
                        <td>{{ $no }}</td>
                        <td>-</td>
                        <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                        <td>{{ $c->nm_anak }}</td>
                        <td>Denda</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>

                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">
                            -
                        </td>
                        <td class="text-end">{{ number_format($c->denda) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="dhead text-center" colspan="5">TOTAL</th>
                    <th class="text-end dhead">{{ number_format($ttlpcs_awal, 0) }}</th>
                    <th class="text-end dhead">{{ number_format($ttlgr_awal, 0) }}</th>
                    <th class="text-end dhead">{{ number_format($ttlpcs_akhir, 0) }}</th>
                    <th class="text-end dhead">{{ number_format($ttlgr_akhir, 0) }}</th>
                    <th class="text-end dhead"></th>
                    <th class="text-end dhead">{{ number_format($ttlttl_rp, 0) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
