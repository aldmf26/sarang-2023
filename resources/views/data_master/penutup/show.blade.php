<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <h6 class=" mt-1">{{ $title }}</h6>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @foreach ($pengawas as $p)
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if ($loop->first) active @endif" id="{{ $p->name }}-tab"
                        data-bs-toggle="tab" href="#{{ $p->name }}" role="tab"
                        aria-controls="{{ $p->name }}"
                        aria-selected="@if ($loop->first) true @else false @endif">{{ strtoupper($p->name) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content" id="myTabContent">
            @foreach ($pengawas as $p)
                @php
                    $datas = DB::table('tb_gaji_penutup')
                        ->where([['pgws', $p->name], ['bulan_dibayar', $bulan]])
                        ->get();
                @endphp
                <div x-data="{
                    cabut: true,
                    eo: true,
                    sortir: true
                }" class="tab-pane fade @if ($loop->first) show active @endif"
                    id="{{ $p->name }}" role="tabpanel" aria-labelledby="{{ $p->name }}-tab">
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <span class="me-2">Filter : </span>
                            <div class="form-check form-check-inline">
                                <input x-model="cabut" type="checkbox" class="pointer form-check-input" id="cabutCheck">
                                <label class="pointer form-check-label" for="cabutCheck">Cabut</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input x-model="eo" type="checkbox" class="pointer form-check-input" id="eoCheck">
                                <label class="pointer form-check-label" for="eoCheck">Eo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input x-model="sortir" type="checkbox" class="pointer form-check-input"
                                    id="sortirCheck">
                                <label class="pointer form-check-label" for="sortirCheck">Sortir</label>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <table class="float-end">
                                <tbody>
                                    <tr>
                                        <td>Pencarian :</td>
                                        <td><input autofocus type="text" id="pencarian"
                                                class="form-control float-end"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <style>
                            tr td {
                                text-align: right
                            }
                        </style>
                        <div class="col-lg-12">
                            <table id="tblAldi" class="table table-stripped table-bordered table-responsive">
                                @php
                                    $bgDanger = 'text-white bg-danger';
                                    // $buka = "<span class='badge bg-secondary float-end'>Buka <i class='fas fa-caret-down'></i></span>";
                                    $buka = '';
                                @endphp
                                <thead>

                                    <tr>
                                        <th class="text-center " colspan="4">#</th>
                                        <th @click="cabut = ! cabut" :class="{ 'd-none': !cabut }"
                                            class="text-center text-white bg-info"
                                            :colspan="cabut ? '8' : ''" ">
                                            Cabut
                                            
                                        </th>
                                        <th @click="eo = ! eo" :class="{ 'd-none': !eo }" class="text-center text-white bg-success" :colspan="eo ? '4' : ''" ">
                                            Cabut Eo
                                        </th>
                                        <th @click="sortir= ! sortir" :class="{ 'd-none': !sortir }"
                                            class="text-center text-white bg-primary" :colspan="sortir ? '6' : ''" " >
                                            Sortir
                                        </th>
                                        <th class="text-center {{ $bgDanger }}" colspan="4">Gajih</th>
                                    </tr>
                                    <tr>
                                        <th class="dhead">Pgws</th>
                                        <th class="dhead">Hari Masuk</th>
                                        <th class="dhead">Nama Karyawan</th>
                                        <th class="dhead">Kelas</th>
            
                                        <th x-show="cabut" class="dhead">Pcs Awal Cbt</th>
                                        <th x-show="cabut" class="dhead">Gr Awal</th>
                                        <th x-show="cabut" class="dhead">Pcs Akhir</th>
                                        <th x-show="cabut" class="dhead">Gr Akhir</th>
                                        <th x-show="cabut" class="dhead">Eot Gr</th>
                                        <th x-show="cabut" class="dhead">Gr Flx</th>
                                        <th x-show="cabut" class="dhead">Susut %</th>
                                        <th x-show="cabut" class="{{ $bgDanger }}">Ttl Rp</th>
            
                                        <th x-show="eo" class="dhead">Gr Eo Awal</th>
                                        <th x-show="eo" class="dhead">Gr Eo Akhir</th>
                                        <th x-show="eo" class="dhead">Susut %</th>
                                        <th x-show="eo" class="{{ $bgDanger }}">Ttl Rp</th>
            
                                        <th x-show="sortir" class="dhead">Pcs Awal Srt</th>
                                        <th x-show="sortir" class="dhead">Gr Awal</th>
                                        <th x-show="sortir" class="dhead">Pcs Akhir</th>
                                        <th x-show="sortir" class="dhead">Gr Akhir</th>
                                        <th x-show="sortir" class="dhead">Susut %</th>
                                        <th x-show="sortir" class="{{ $bgDanger }}">Ttl Rp</th>
            
                                        <th class="dhead">Kerja Dll</th>
                                        <th class="dhead">Rp Denda</th>
                                        <th class="{{ $bgDanger }}">Ttl Gaji</th>
                                        <th class="dhead">Rata2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                      @foreach ($datas as $data)
                                    <tr>
                                        <td>{{ $data->pgws }}</td>
                                        <td>{{ $data->hari_masuk }}</td>
                                        <td>{{ $data->nama }}</td>
                                        <td>{{ $data->kelas }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_pcs_awal, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_gr_awal, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_pcs_akhir, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_gr_akhir, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_eot, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_flx, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_sst, 0) }}</td>
                                        <td x-show="cabut">{{ number_format($data->cbt_ttlrp, 0) }}</td>

                                        <td x-show="eo">{{ number_format($data->eo_gr_awal, 0) }}</td>
                                        <td x-show="eo">{{ number_format($data->eo_gr_akhir, 0) }}</td>
                                        <td x-show="eo">{{ number_format($data->eo_sst, 0) }}</td>
                                        <td x-show="eo">{{ number_format($data->eo_ttlrp, 0) }}</td>

                                        <td x-show="sortir">{{ number_format($data->srt_pcs_awal, 0) }}</td>
                                        <td x-show="sortir">{{ number_format($data->srt_gr_awal, 0) }}</td>
                                        <td x-show="sortir">{{ number_format($data->srt_pcs_akhir, 0) }}</td>
                                        <td x-show="sortir">{{ number_format($data->srt_gr_akhir, 0) }}</td>
                                        <td x-show="sortir">{{ number_format($data->srt_sst, 0) }}</td>
                                        <td x-show="sortir">{{ number_format($data->srt_ttlrp, 0) }}</td>

                                        <td>{{ number_format($data->dll, 0) }}</td>
                                        <td>{{ number_format($data->denda, 0) }}</td>
                                        <td>{{ number_format($data->ttl_gaji, 0) }}</td>
                                        <td>{{ number_format($data->ratarata, 0) }}</td>
                                    </tr>
            @endforeach
            </tbody>
            <tfoot class="bg-info text-white">
                <tr>
                    <td colspan="4" class="text-center">Total</td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_pcs_awal'), 0) }}</td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_gr_awal'), 0) }}</td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_pcs_akhir'), 0) }}</td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_gr_akhir'), 0) }}</td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_eot'), 0) }}</td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_flx'), 0) }}</td>
                    <td x-show="cabut"></td>
                    <td x-show="cabut">{{ number_format($datas->sum('cbt_ttlrp'), 0) }}</td>

                    <td x-show="eo">{{ number_format($datas->sum('eo_gr_awal'), 0) }}</td>
                    <td x-show="eo">{{ number_format($datas->sum('eo_gr_akhir'), 0) }}</td>
                    <td x-show="eo"></td>
                    <td x-show="eo">{{ number_format($datas->sum('eo_ttlrp'), 0) }}</td>

                    <td x-show="sortir">{{ number_format($datas->sum('srt_pcs_awal'), 0) }}</td>
                    <td x-show="sortir">{{ number_format($datas->sum('srt_gr_awal'), 0) }}</td>
                    <td x-show="sortir">{{ number_format($datas->sum('srt_pcs_akhir'), 0) }}</td>
                    <td x-show="sortir">{{ number_format($datas->sum('srt_gr_akhir'), 0) }}</td>
                    <td x-show="sortir"></td>
                    <td x-show="sortir">{{ number_format($datas->sum('srt_ttlrp'), 0) }}</td>

                    <td>{{ number_format($datas->sum('dll'), 0) }}</td>
                    <td>{{ number_format($datas->sum('denda'), 0) }}</td>
                    <td>{{ number_format($datas->sum('ttl_gaji'), 0) }}</td>
                    <td></td>
                </tr>

            </tfoot>
            </table>
        </div>
        </div>

        </div>
        @endforeach
        </div>

    </x-slot>

    <x-slot name="cardBody">

        @section('scripts')
            <script>
                pencarian('pencarian', 'tblAldi')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
