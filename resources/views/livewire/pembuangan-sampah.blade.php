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
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <button wire:loading wire:target='ceklis,tbhParaf,pilihanLimbah,selectedBulan'
                class="btn btn-secondary btn-sm" type="button" disabled="">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Processing...
            </button>



            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        @php
                            $class = 'text-center align-middle dhead';
                        @endphp
                        <th class="{{ $class }}">Tanggal</th>
                        <th class="{{ $class }}">Jam Checklist</th>
                        <th class="{{ $class }}">CEKLIS (✓)</th>
                        <th class="{{ $class }}">Paraf Petugas</th>
                        <th class="{{ $class }}">Keterangan</th>
                    </tr>
                </thead>
                <tbody>

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
                                        <input @checked($this->cekJam($this->pilihanLimbah, $jam['time'], $selectedBulan, $i)) class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $parafData = DB::table($tbl)
                                            ->where('jenis_sampah', $this->pilihanLimbah)
                                            ->where('tgl', "2025-$selectedBulan-$i")
                                            ->where('jam_cek', $jam['time'])
                                            ->value('paraf_petugas');

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
                                                <a wire:click.prevent="tbhParaf('paraf_petugas', '{{ $d->name }}', '{{ "2025-$selectedBulan-$i" }}', '{{ $jam['time'] }}')"
                                                    class="dropdown-item">
                                                    {{ $d->name }}
                                                    {{ $parafData == $d->name ? '☑️' : '' }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td
                                    style="word-wrap: break-word; word-break: break-word; white-space: normal; max-width: 200px;">
                                    <div class="d-flex gap-2 justify-content-between" x-data="{
                                        showEdit: false
                                    }">

                                        @if ($this->cekJam($this->pilihanLimbah, $jam['time'], $selectedBulan, $i))
                                            @php
                                                $keterangan = DB::table($this->tbl)
                                                    ->where('jenis_sampah', $this->pilihanLimbah)
                                                    ->where('tgl', "2025-$selectedBulan-$i")
                                                    ->where('jam_cek', $jam['time'])
                                                    ->value('ket');
                                            @endphp
                                            @if ($keterangan)
                                                <p x-show="!showEdit">{{ $keterangan }}</p>
                                                <div class="w-100" x-show="showEdit">
                                                    <input @click.outside="showEdit = false"
                                                        wire:change="saveKeterangan('{{ $i }}', '{{ $jam['time'] }}')"
                                                        wire:model.lazy="keterangan.{{ $i }}.{{ $jam['time'] }}"
                                                        x-show="showEdit" type="text"
                                                        class="form-control form-control-sm" :value="@js($keterangan)"
                                                        @blur="() => { showEdit = true }">

                                                </div>
                                                <p class="pointer badge bg-info" @click="showEdit = !showEdit"
                                                    x-text="showEdit ? 'Batal' : 'Edit'">
                                                </p>
                                            @else
                                                <input
                                                    wire:model.blur="keterangan.{{ $i }}.{{ $jam['time'] }}"
                                                    wire:change="saveKeterangan('{{ $i }}', '{{ $jam['time'] }}')"
                                                    type="text" name="keterangan"
                                                    class="form-control form-control-sm" placeholder="Keterangan">
                                            @endif
                                        @endif



                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endfor

                </tbody>
            </table>
        </div>
    </div>
</div>
