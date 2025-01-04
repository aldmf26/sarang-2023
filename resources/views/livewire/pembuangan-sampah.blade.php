<div>
  
    <div class="row">
        <div class="col-lg-3">
            <div class="mb-3">
                <label for="bulan" class="form-label">Pilih Bulan</label>
                <select required wire:model.live="selectedBulan" id="bulan" class="form-select">
                    <option value="">Pilih bulan</option>
                    @foreach ($bulans as $b)
                        <option value="{{ $b->bulan }}">{{ $b->nm_bulan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="mb-3">
                <label for="jenisLimbah" class="form-label">Jenis Limbah</label>
                <select required wire:model.live="pilihanLimbah" id="jenisLimbah" class="form-select">
                    <option value="">Jenis Limbah</option>
                    @foreach ($jenisLimbah as $b)
                        <option @selected($b == $jenis_limbah) value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <button wire:loading wire:target='ceklis,pilihanLimbah,selectedBulan' class="btn btn-secondary btn-sm"
                type="button" disabled="">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Processing...
            </button>

            <x-theme.alert />
            
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="text-center align-middle dhead">Tanggal</th>
                        <th rowspan="2" class="text-center align-middle dhead">Jam Checklist</th>
                        <th rowspan="2" class="text-center align-middle dhead">CEKLIS (✓)</th>
                        <th rowspan="2" class="text-center align-middle dhead">Paraf Petugas</th>
                        <th rowspan="2" class="text-center align-middle dhead">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $jamList = collect([
                            ['time' => '07:00:00', 'label' => 'AM'],
                            ['time' => '04:00:00', 'label' => 'PM'],
                        ]);

                        function cekJam($jenisSampah, $jam, $bulan, $hari)
                        {
                            return DB::table('hrga7_pembuangan_sampah')
                                ->where('jenis_sampah', $jenisSampah)
                                ->where('jam_cek', $jam)
                                ->whereMonth('tgl', $bulan)
                                ->whereDay('tgl', $hari)
                                ->exists();
                        }
                    @endphp

                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @foreach ($jamList as $index => $jam)
                            <tr>
                                @if ($index === 0)
                                    <td class="text-center" rowspan="2">{{ $i }}</td>
                                @endif
                                <td class="text-center">{{ $jam['time'] }} {{ $jam['label'] }}</td>
                                <td class="text-center pointer">
                                    <div wire:click='ceklis("{{ $i }}", "{{ $jam['time'] }}")'
                                        class="form-check d-flex justify-content-center">
                                        <input @checked(cekJam($this->pilihanLimbah, $jam['time'], $selectedBulan, $i)) class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endfor

                </tbody>
            </table>
        </div>
    </div>
</div>
