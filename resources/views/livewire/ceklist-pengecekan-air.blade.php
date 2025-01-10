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
                <label for="jenisLimbah" class="form-label">Jenis Mesin</label>
                <select required wire:model.live="selectedJenisMesin" id="jenisMesin" class="form-select">
                    <option value="">Jenis Mesin</option>
                    @foreach ($jenisMesin as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-12">
            <button wire:loading wire:target='selectedBulan,selectedJenisMesin' class="mb-2 btn btn-secondary btn-sm"
                type="button" disabled="">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Processing...
            </button>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center dhead">Tanggal</th>
                        <th class="text-center dhead">Kondisi</th>
                        <th class="text-center dhead">Kondisi air yang dihasilkan bebas bau, tidak bewarna </th>
                        <th class="text-center dhead">pemeriksa</th>
                        <th class="text-center dhead">paraf</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td>
                                <input wire:change="ubah('kondisi',{{ $i }},$event.target.value)"
                                    type="text" class="form-control form-control-sm" placeholder="kondisi">
                            </td>
                            <td>
                                <input wire:change="ubah('kondisi_air',{{ $i }},$event.target.value)" type="text" class="form-control form-control-sm" placeholder="kondisi air">
                            </td>
                            <td>
                                <input wire:change="ubah('pemeriksa',{{ $i }},$event.target.value)" type="text" class="form-control form-control-sm" placeholder="pemeriksa">
                            </td>
                            <td>
                                @php
                                    $parafData = DB::table($tbl)
                                        ->where('jenis_mesin', $this->selectedJenisMesin)
                                        ->where('tgl', "2025-$selectedBulan-$i")
                                        ->value('paraf');

                                @endphp
                                <div class="dropdown dropdown-color-icon" bis_skin_checked="1">
                                    <button
                                        class="btn btn-xs btn-block btn-{{ $parafData ? '' : 'outline-' }}info dropdown-toggle"
                                        type="button" id="dropdownMenuButtonEmoji" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <span class="me-50">🧑‍🚒</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonEmoji"
                                        bis_skin_checked="1" style="">
                                        @foreach ($adminSanitasi['petugas'] as $d)
                                            <a wire:click.prevent="tbhParaf('paraf_petugas', '{{ $d->name }}', '{{ "2025-$selectedBulan-$i" }}')"
                                                class="dropdown-item">
                                                {{ $d->name }}
                                                {{ $parafData == $d->name ? '☑️' : '' }}

                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
