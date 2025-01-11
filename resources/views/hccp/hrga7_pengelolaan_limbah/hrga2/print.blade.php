<x-theme.hccp_print :title="$title" :dok="$dok">
    @php
        // Daftar jenis limbah yang valid
        $validLimbah = ['Bulu', 'Organik', 'Non Organik'];
    @endphp

    Jenis Limbah:
    @foreach ($validLimbah as $limbah)
        @if (strtolower($jenis_limbah) !== strtolower($limbah))
            <s>{{ $limbah }}</s>
        @else
            {{ $limbah }}
        @endif
        @if (!$loop->last)
            /
        @endif
    @endforeach
    <br>
    Bulan : {{ $nm_bulan }}


    <div class="row">

        <div class="col-sm-12 col-lg-12">
            <table class="table table-bordered border-dark">
                <thead>
                    <tr>
                        @php
                            $class = 'text-center align-middle fs-bold';
                        @endphp
                        <th class="{{ $class }}">Tanggal</th>
                        <th class="{{ $class }}">Jam Pengeluaran</th>
                        <th class="{{ $class }}">CEKLIS (✓)</th>
                        <th class="{{ $class }}">Paraf <br> Petugas</th>
                        <th class="{{ $class }}">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!function_exists('cekJam'))
                        @php
                            function cekJam($jenisSampah, $bulan, $hari)
                            {
                                return DB::table('hrga7_pembuangan_tps')
                                    ->where('jenis_sampah', $jenisSampah)
                                    ->whereMonth('tgl', $bulan)
                                    ->whereDay('tgl', $hari)
                                    ->first();
                            }
                        @endphp
                    @endif
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @foreach ($jamList as $index => $jam)
                            @php
                                $loadCekJam = cekJam($jenis_limbah, $selectedBulan, $i);
                            @endphp
                            <tr>
                                @if ($index === 0)
                                <td class="text-center">{{ $i }}</td>
                                @endif
                                <td class="text-center align-middle">
                                    {{ !empty($loadCekJam->jam_cek) ? \Carbon\Carbon::parse($loadCekJam->jam_cek)->format('g:i A') : '' }}
                                </td>
                                <td class="text-center pointer">
                                    {{ $loadCekJam ? '√' : '' }}
                                </td>
                                <td align="center">
                                    {{$loadCekJam->paraf_petugas ?? ''}}
                                </td>
                                <td style="word-wrap: break-word; word-break: break-word; white-space: normal; max-width: 200px;">
                                    {{$loadCekJam->ket ?? ''}}
                                </td>
                            </tr>
                        @endforeach
                    @endfor

                </tbody>
            </table>
        </div>
        <div class="col-sm-6 col-lg-4">
            <span>√ : Diambil semua</span>
        </div>
        <div class="col-sm-6 col-lg-4">
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
