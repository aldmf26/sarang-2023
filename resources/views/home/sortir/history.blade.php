<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered" id="tblHistory">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Tipe</th>
                    <th class="dhead">Pengawas</th>
                    <th class="dhead">Anak</th>
                    <th class="dhead">Tgl Terima</th>
                    <th class="text-end dhead">Pcs Awal</th>
                    <th class="text-end dhead">Gr Awal</th>
                    <th class="text-end dhead">Pcs Akhir</th>
                    <th class="text-end dhead">Gr Akhir</th>
                    <th class="text-end dhead">Susut</th>
                    <th class="text-end dhead">Denda</th>
                    <th class="text-end dhead">Ttl Gaji</th>
                   
                </tr>
            </thead>
            <tbody>
                @foreach ($cabut as $no => $d)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
                                id_sortir="{{ $d->id_sortir }}">{{ $d->no_box }}</a>
                        </td>
                        <td>{{ $d->kelas }}</td>
                        <td>{{ ucwords(auth()->user()->name) }}</td>
                        <td>{{ $d->nama }}</td>
                        <td>{{ $d->tgl }}</td>
                        <td align="right">{{ $d->pcs_awal ?? 0 }}</td>
                        <td align="right">{{ $d->gr_awal ?? 0 }}</td>
                        <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                        <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                        @php
                            $susut = empty($d->gr_akhir) ? 0 : (1 - $d->gr_akhir / $d->gr_awal) * 100;
                        @endphp
                        <td align="right">{{ number_format($susut, 0) }}%</td>
                        <td align="right">{{ number_format($d->denda_sp ?? 0, 0) }}</td>
                        <td align="right">{{ number_format($d->ttl_rp ?? 0, 0) }}</td>
        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

