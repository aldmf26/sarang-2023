<x-theme.hccp_print :title="$title" :dok="$dok">
<span>Bulan/ Tahun : {{ $nm_bulan }} / {{ $tahun }}</span><br>
<span>Jenis mesin : {{$jenis_mesin}}</span>
    <div class="row">

        <div class="col-sm-12 col-lg-12">
            <table class="table table-bordered border-dark">
                <thead>
                    <tr>
                        @php
                            $class = 'text-center align-middle fs-bold';
                        @endphp
                        <th class="{{ $class }}">Tanggal</th>
                        <th class="{{ $class }}">Kondisi</th>
                        <th width="250" class="{{ $class }}">Kondisi air yang dihasilkan bebas bau, tidak
                            bewarna </th>
                        <th class="{{ $class }}">pemeriksa</th>
                        <th class="{{ $class }}">paraf</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @php
                            $datas = DB::table('hrga8_ceklist_pengecekan_air')
                                ->where('tgl', "$tahun-$bulan-$i")
                                ->first();
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td>{{ $datas->kondisi ?? '' }}</td>
                            <td>{{ $datas->kondisi_air ?? '' }}</td>
                            <td>{{ $datas->pemeriksa ?? '' }}</td>
                            <td>{{ $datas->paraf ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="col-sm-6 col-lg-4">
            Ket : √ (nyala) , x (tidak nyala) <br>
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
