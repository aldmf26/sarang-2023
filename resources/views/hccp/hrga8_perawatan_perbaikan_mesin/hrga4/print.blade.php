<x-theme.hccp_print :title="$title" :dok="$dok">
    <span>Ruangan : {{ $ruangan }}</span><br>
    <span>Bulan/ Tahun : {{ $nm_bulan }} / {{ $tahun }}</span><br>
    <span>Standard Suhu : {{$standard}}</span>
        <div class="row">
    
            <div class="col-sm-12 col-lg-12">
                <table class="table table-bordered border-dark">
                    <thead>
                        <tr>
                            @php
                                $class = 'text-center align-middle fs-bold';
                            @endphp
                            <th class="{{ $class }}">Tanggal</th>
                            <th class="{{ $class }}">Suhu</th>
                            <th class="{{ $class }}">pemeriksa</th>
                            <th class="{{ $class }}">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $datas = DB::table('hrga8_ceklist_suhu_cold_storage')
                                    ->where('tgl', "$tahun-$bulan-$i")
                                    ->first();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $datas->suhu ?? '' }}</td>
                                <td>{{ $datas->pemeriksa ?? '' }}</td>
                                <td>{{ $datas->ket ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6 col-lg-4">
                <br>
                <em>* Coret salah satu</em>
            </div>
            <div class="col-sm-2"></div>
            <div class="col-sm-4 col-lg-4">
                <table style="border: 1px solid black; border-collapse: collapse; width: 100%; margin-top: 10px;">
                    <tr>
                        <td style="border: 1px solid black; text-align: center;">Diketahui Oleh:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; text-align: center; height: 80px; vertical-align: bottom;">[SPV.
                            GA-IR]</td>
                    </tr>
                </table>
            </div>
        </div>
    
    </x-theme.hccp_print>
    