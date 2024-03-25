<div class="row">

    <div class="col-lg-12">
        <table class="table table-hover table-bordered" style="border:1px solid #97a1c3" id="tblHistory">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead" width="50">Bulan</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Pgws</th>
                    <th class="dhead">Nama</th>
                    <th class="dhead">Kelas</th>
                    <th class="dhead">Tgl Ambil</th>
                    <th class="dhead" class="text-end">Gr EO <br> Awal ~ Akhir</th>
                    <th class="dhead text-end">Susut</th>
                    <th class="dhead text-end">Ttl Rp</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cabut as $no => $d)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ !empty($d->bulan_dibayar) ? date('M y', strtotime('01-' . $d->bulan_dibayar . '-' . date('Y'))) : '' }}
                        </td>
                        <td>
                            {{-- <a href="#" data-bs-toggle="modal" data-bs-target="#detail" class="detail"
                                id_cabut="{{ $d->id_eo }}">{{ $d->no_box }}</a> --}}
                            {{ $d->no_box }}
                        </td>
                        <td>{{ $d->name }}</td>
                        <td>{{ $d->nama }}</td>
                        <td>{{ $d->kelas }} - {{ number_format($d->rupiah, 0) }}</td>
                        <td>{{ date('d M y', strtotime($d->tgl_ambil)) }}</td>
                        <td align="right">
                            {{ number_format($d->gr_eo_awal, 0) . ' ~ ' . number_format($d->gr_eo_akhir, 0) }}</td>
                        @php
                            $susut = empty($d->gr_eo_awal) ? 0 : (1 - $d->gr_eo_akhir / $d->gr_eo_awal) * 100;
                        @endphp
                        <td align="right">{{ number_format($susut, 0) }}%</td>
                        <td align="right">{{ number_format($d->ttl_rp, 0) }}
                        </td>
                       
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
