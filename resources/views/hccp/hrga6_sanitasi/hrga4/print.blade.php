<x-theme.hccp_print :title="$title" :dok="$dok">
    Bulan : {{ $nm_bulan }} 2025<br>
    Area : {{ $area }}
    <table style="font-size: 11px" class="table table-bordered border-dark">
        <thead style="background-color: #C0C0C0" class=" text-white">
            <tr>
                <th class="text-center text-dark">Item Pembersihan</th>
                <th class="text-center text-dark">Frekuensi</th>
                <th class="text-center text-dark">Ttl</th>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    <th class="text-center text-dark">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php
                $currentItem = null; // Untuk melacak item saat ini
                $rowCount = 0; // Untuk menghitung jumlah frekuensi per item
            @endphp
            @foreach ($foothbathTemplate as $key => $row)
                @php
                    // Hitung jumlah baris untuk item yang sama
                    if ($currentItem !== $row->item) {
                        $currentItem = $row->item;
                        $rowCount = $foothbathTemplate->where('item', $row->item)->count();
                    } else {
                        $rowCount = 0;
                    }
                @endphp
                <tr>
                    @if ($rowCount > 0)
                        <td class="align-middle" rowspan="{{ $rowCount }}">{{ $row->item }}</td>
                    @endif
                    <td class="align-middle">{{ $row->frekuensi }}</td>
                    <td class="text-center">√ : {{ $row->ttl_status_1 }} <br> x : {{ $row->ttl_status_2 }}</td>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @php
                            $firstSanitasi = DB::table('foothbath_ceklis')
                                ->where('id_lokasi', $id_lokasi)
                                ->where('id_frekuensi', $row->id)
                                ->whereMonth('tgl', $bulan)
                                ->whereDay('tgl', $i)
                                ->first();

                            $cekSama = !empty($firstSanitasi);
                            $cekStatus = !empty($firstSanitasi) ? ($firstSanitasi->status == 1 ? '√' : 'x') : '';
                        @endphp
                        <td align="center" class="pointer align-middle">
                            {{ $cekStatus ?? '' }}
                        </td>
                    @endfor
                </tr>
            @endforeach

            <tr>
                <td>Paraf Petugas</td>
                <td>Ttd</td>

                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $parafData = DB::table('foothbath_ceklis')
                            ->where('id_lokasi', $id_lokasi)
                            ->where('tgl', "$tahun-$bulan-$i")
                            ->value('paraf_petugas');

                    @endphp
                    <td style="font-size: 10px" class="pointer">
                        {{ Str::limit($parafData ?? '', 3, '') }}
                    </td>
                @endfor
            </tr>

            <tr>
                <td>Verifikator</td>
                <td>Ttd</td>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $parafData = DB::table('foothbath_ceklis')
                            ->where('id_lokasi', $id_lokasi)
                            ->where('tgl', "$tahun-$bulan-$i")
                            ->value('verifikator');

                    @endphp
                    <td class="pointer">
                        {{ Str::limit($parafData ?? '', 3, '') }}
                    </td>
                @endfor
            </tr>
        </tbody>
    </table>

    <div class="row">
        <div class="col-lg-4">
            <table cellpadding="5" cellspacing="2">
                <tr>
                    <td>Keterangan</td>
                    <td>√ : standar minimal 200 ppm</td>
                </tr>
                <tr>
                    <td></td>
                    <td>x : < 200 ppm</td>
                </tr>
            </table>
        </div>
        <div class="col-lg-2"></div>

        <div class="col-lg-4">
            <table style="border: 1px solid black; border-collapse: collapse; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="border: 1px solid black; text-align: center;">Diperiksa Oleh:</td>
                    <td style="border: 1px solid black; text-align: center;">Diketahui Oleh:</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: center; height: 80px; vertical-align: bottom;"></td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: bottom;"></td>
                </tr>
            </table>
        </div>
        <div class="col-lg-2">
            <table style="border: 1px solid black; border-collapse: collapse; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="border: 1px solid black; text-align: center;">Diverifikasi Oleh:
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: center; height: 80px; vertical-align: bottom;">[
                        Dokter Hewan ]
                    </td>
                </tr>
            </table>
        </div>
    </div>

</x-theme.hccp_print>
