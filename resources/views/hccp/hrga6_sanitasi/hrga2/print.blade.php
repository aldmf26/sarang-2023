<x-theme.hccp_print :title="$title" :dok="$dok">
    Bulan : {{ $nm_bulan }} <br>
    Area : {{$area}}
    <table style="font-size: 13px" class="table table-bordered text-center border-dark">
        <thead class="bg-info text-white">
            <tr>
                <th style="background-color: #D9D9D9; color:black;">Item Pembersihan</th>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    <th style="background-color: #D9D9D9; color:black;">{{ $i }}</th>
                @endfor
            </tr>

        </thead>
        <tbody>
            @foreach ($itemSanitasi as $d)
                <tr>
                    <td>{{ $d->nama_item }}</td>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @php
                            $firstSanitasi = DB::table('sanitasi')
                                ->where('id_lokasi', $id_lokasi)
                                ->where('id_item', $d->id_item)
                                ->whereDay('tgl', $i)
                                ->first();
                            $cekSama = empty($firstSanitasi) ? false : true;
                        @endphp
                        <td class="pointer">
                            @if ($cekSama)
                                V
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach

            {{-- ini tambah item pemberrsihan --}}
                <tr rowspan="2">
                    <td></td>
                </tr>
            <tr>    
                <td>Paraf Petugas</td>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $cekSamaPetugas = DB::table('sanitasi')
                            ->where('id_lokasi', $id_lokasi)
                            ->whereMonth('paraf_petugas', $bulan)
                            ->whereDay('paraf_petugas', $i)
                            ->whereNotNull('paraf_petugas')
                            ->exists();
                    @endphp
                    <td class="pointer">
                        @if ($cekSamaPetugas)
                            V
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Verifikator</td>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $cekSamaPetugas = DB::table('sanitasi')
                            ->where('id_lokasi', $id_lokasi)
                            ->whereMonth('verifikator', $bulan)
                            ->whereDay('verifikator', $i)
                            ->whereNotNull('verifikator')
                            ->exists();
                    @endphp
                    <td class="pointer">
                        @if ($cekSamaPetugas)
                            V
                        @endif
                        {{-- <input @checked($cekSamaPetugas) class="form-check-input" type="checkbox" /> --}}
                    </td>
                @endfor
            </tr>

        </tbody>
    </table>

    <div class="row">
        <div class="col-lg-4">
        </div>
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <table style="border: 1px solid black; border-collapse: collapse; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="border: 1px solid black; text-align: center;">Diperiksa Oleh:</td>
                    <td style="border: 1px solid black; text-align: center;">Diketahui Oleh:</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: center; height: 80px; vertical-align: bottom;">[SPV.
                        GA-IR]</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: bottom;">[KA.HRGA]</td>
                </tr>
            </table>
        </div>
    </div>

</x-theme.hccp_print>
