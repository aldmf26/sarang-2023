<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="cetak-tab" data-bs-toggle="tab" href="#cetak" role="tab" aria-controls="home"
            aria-selected="true">Box dari Cetak</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="grade-tab" data-bs-toggle="tab" href="#grade" role="tab" aria-controls="profile"
            aria-selected="false" tabindex="-1">Grading</a>
    </li>

</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade active show" id="cetak" role="tabpanel" aria-labelledby="cetak-tab">
        <div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table">
                        <tr>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">Ket</th>
                            <th class="dhead">Partai BJ</th>
                        </tr>
                        <tr>
                            <td>
                                <input readonly type="date" value="{{ $box[0]->tgl }}" class="form-control">
                            </td>
                            <td>
                                <input readonly type="text" value="{{ $box[0]->ket }}" placeholder="ket"
                                    class="form-control">
                            </td>
                            <td>
                                <input readonly type="text" value="{{ $box[0]->partai }}" placeholder="partai bj"
                                    class="form-control">
                            </td>

                        </tr>
                    </table>
                    <table class="table table-hover table-bordered">
                        <thead class="">
                            <tr>
                                <th class="dhead ">No Box</th>
                                <th class="dhead  text-end">Pcs Awal</th>
                                <th class="dhead  text-end">Gr Awal</th>
                                <th class="dhead  text-end">Pcs Akhir</th>
                                <th class="dhead  text-end">Gr Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $pcs = 0;
                                $gr = 0;
                                $pcs_akhir = 0;
                                $gr_akhir = 0;
                            @endphp
                            @foreach ($box as $d)
                                @php
                                    $pcs += $d->pcs_awal;
                                    $gr += $d->gr_awal;
                                    $pcs_akhir += $d->pcs_akhir;
                                    $gr_akhir += $d->gr_akhir;
                                @endphp
                                <tr>
                                    <td>{{ $d->no_box }}</td>
                                    <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->pcs_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Ttl Box : {{ count($box) }}</th>
                                <th class="text-end">{{ number_format($pcs, 0) }}</th>
                                <th class="text-end">{{ number_format($gr, 0) }}</th>
                                <th class="text-end">{{ number_format($pcs_akhir, 0) }}</th>
                                <th class="text-end">{{ number_format($gr_akhir, 0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade show" id="grade" role="tabpanel" aria-labelledby="grade-tab">
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-hover table-bordered">
                    <thead class="">
                        <tr>
                            <th class="dhead ">Grade</th>
                            <th class="dhead  text-end">Pcs </th>
                            <th class="dhead  text-end">Gr </th>
                            <th class="dhead  text-end">Rp/Gram </th>
                            <th class="dhead  text-end">Ttl Rp </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pcs = 0;
                            $gr = 0;
                            $ttl_rp = 0;
                        @endphp
                        @foreach ($listGrading as $no => $d)
                            @php
                                $pcs += $d->pcs;
                                $gr += $d->gr;
                                $ttl_rp += $d->rp_gram * $d->gr;
                            @endphp
                            <tr>
                                <td>{{ $d->grade }}</td>
                                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                <td align="right">{{ number_format($d->gr, 0) }}</td>
                                <td align="right">{{ number_format($d->rp_gram, 0) }}</td>
                                <td align="right">{{ number_format($d->rp_gram * $d->gr, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Grand Total</th>
                            <th class="text-end">{{ number_format($pcs, 0) }}</th>
                            <th class="text-end">{{ number_format($gr, 0) }}</th>
                            <th class="text-end">{{ number_format($ttl_rp / $gr, 0) }}</th>
                            <th class="text-end">{{ number_format($ttl_rp, 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
