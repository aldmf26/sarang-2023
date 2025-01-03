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
                <label for="bulan" class="form-label">Jenis Limbah</label>
                <select required wire:model.live="selectedJenisLImbah" id="jenisLimbah" class="form-select">
                    <option value="">Jenis Limbah</option>
                    @foreach ($jenisLimbah as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="text-center align-middle">Tanggal</th>
                        <th rowspan="2" class="text-center align-middle">Jam Checklist</th>
                        <th rowspan="2" class="text-center align-middle">CEKLIS (✓)</th>
                        <th rowspan="2" class="text-center align-middle">Paraf Petugas</th>
                        <th rowspan="2" class="text-center align-middle">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $daysInMonth; $i++)
                    <tr>
                        <td class="text-center" rowspan="2">{{ $i + 1 }}</td>
                        <td class="text-center">07:00:00 AM</td>
                        <td class="text-center pointer">
                            <div wire:click='ceklis("{{ $i + 1 }}", "07:00:00")' class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">04:00:00 PM</td>
                        <td class="text-center pointer">
                            <div wire:click='ceklis("{{ $i + 1 }}", "04:00:00")' class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                    
                </tbody>
            </table>
        </div>
    </div>
</div>