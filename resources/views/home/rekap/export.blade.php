<table class="table" id="table1">
    <thead>
        <tr>
            <th colspan="11">Cabut</th>
            <th colspan="6">Cabut Spc</th>
            <th colspan="3">Cabut Eo</th>
            <th colspan="5">Sortir</th>
            <th>DLL</th>
            <th colspan="3">Gajih</th>
        </tr>
        <tr>
            {{-- Cabut --}}
            <th>Pengawas</th>
            <th class="text-end ">Hari Masuk</th>
            <th>Nama Anak</th>
            <th>Kelas</th>
            <th class="text-end ">Pcs Awal</th>
            <th class="text-end ">Gr Awal</th>
            <th class="text-end ">Pcs Akhir</th>
            <th class="text-end ">Gr Akhir</th>
            <th class="text-end ">Eot gr</th>
            <th class="text-end ">Gr Flx</th>
            <th class="text-end ">Ttl Rp</th>
            {{-- Cabut --}}

            {{-- Special --}}
            <th class="text-end">Pcs spc Awal</th>
            <th class="text-end">Gr spc Awal</th>
            <th class="text-end">Pcs spc Akhir</th>
            <th class="text-end">Gr spc Akhir</th>
            <th class="text-end">Eot spc gr</th>
            <th class="text-end ">Ttl Rp Spc</th>
            {{-- Special --}}

            {{-- eo --}}
            <th class="text-end">Gr eo awal</th>
            <th class="text-end">Gr eo akhir</th>
            <th class="text-end">Ttlrp eo</th>
            {{-- eo --}}

            {{-- Sortir --}}
            <th class="text-end">Pcs sp awal</th>
            <th class="text-end">Gr sp awal</th>
            <th class="text-end">Pcs sp akhir</th>
            <th class="text-end">Gr sp akhir</th>
            <th class="text-end">Ttlrp sp</th>
            {{-- Sortir --}}
            {{-- DLL --}}
            <th class="text-end">Kerja dll</th>
            {{-- DLL --}}


            <th class="text-end">Rp Denda</th>
            <th class="text-end">Total Gaji</th>
            <th class="text-end">Rata-rata</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
        <tr>
            {{-- Cabut --}}
            <td>{{$d->name}}</td>
            <td>{{$d->absen}}</td>
            <td>{{$d->nama}}</td>
            <td>{{$d->id_kelas}}</td>
            <td>{{$d->pcs_awal}}</td>
            <td>{{$d->gr_awal}}</td>
            <td>{{$d->pcs_akhir}}</td>
            <td>{{$d->gr_akhir}}</td>
            <td>{{$d->eot}}</td>
            <td>{{$d->gr_flx}}</td>
            <td>{{$d->rupiah - $d->d_susut - $d->d_hcr +
                $d->eot_lebih}}</td>
            {{-- Cabut --}}

            {{-- Cabut SPC --}}
            <td>{{$d->pcs_w_spc}}</td>
            <td>{{$d->gr_w_spc}}</td>
            <td>{{$d->pcs_k_spc}}</td>
            <td>{{$d->gr_k_spc}}</td>
            <td>{{$d->eot_spc}}</td>
            <td>{{$d->rp_spesial}}</td>
            {{-- Cabut SPC --}}

            {{-- EO --}}
            <td>{{$d->gr_eo_awal}}</td>
            <td>{{$d->gr_eo_akhir}}</td>
            <td>{{$d->rp_eo}}</td>
            {{-- EO --}}

            {{-- Sortir --}}
            <td>{{$d->pcs_awal_s}}</td>
            <td>{{$d->gr_awal_s}}</td>
            <td>{{$d->pcs_akhir_s}}</td>
            <td>{{$d->gr_akhir_s}}</td>
            <td>{{$d->rp_sortir}}</td>
            {{-- Sortir --}}
        </tr>
        @endforeach
    </tbody>
</table>