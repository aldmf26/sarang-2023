<div>
    <div class="container">
        <h5>Checklist Foothbath</h5>
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
                    <label for="area" class="form-label">Pilih Area</label>
                    <div wire:ignore>
                        <select required wire:model.live="selectedArea" id="area"
                            class="form-select select2-alpine" x-data="{ selectedOption: @entangle('selectedArea') }" x-init="initSelect2()">
                            <option value="">Pilih area</option>
                            @foreach ($lokasis as $l)
                                <option :selected="selectedOption == '{{ $l->id }}'" value="{{ $l->id }}">
                                    {{ $l->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @section('scripts')
                        <script>
                            function initSelect2() {
                                $('#area').select2().on('change', function(e) {
                                    // Trigger Livewire event when Select2 value changes
                                    @this.set('selectedArea', $(this).val());
                                });
                            }
                        </script>
                    @endsection
                </div>
            </div>
        </div>
        <!-- Pilih Bulan -->

        <!-- Pilih Area -->
        <!-- Tabel Checklist Sanitasi -->
        <div class="table-responsive">

            <table cellpadding="5" cellspacing="2">
                <tr>
                    <td>Keterangan</td>
                    <td>√ : standar minimal 200 ppm</td>
                    <td>(klik kiri)</td>
                </tr>
                <tr>
                    <td></td>
                    <td>x : < 200 ppm</td>
                    <td>(klik kanan)</td>
                </tr>
            </table>

            <button wire:loading wire:target='tbhSanitasi,tbhParaf,addRow,selectedArea,selectedBulan'
                class="mb-2 btn btn-secondary btn-sm" type="button" disabled="">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Processing...
            </button>
            <x-theme.alert />

            <table class="table table-bordered ">
                <thead class="bg-info text-white">
                    <tr>
                        <th class="text-center">Item Pembersihan</th>
                        <th class="text-center">Frekuensi</th>
                        <th class="text-center">Total</th>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <th class="text-center">{{ $i }}</th>
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
                                <td rowspan="{{ $rowCount }}">{{ $row->item }}</td>
                            @endif
                            <td>{{ $row->frekuensi }}</td>
                            <td class="text-center">√ : {{ $row->ttl_status_1 }} <br> x : {{ $row->ttl_status_2 }}</td>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                @php
                                    $firstSanitasi = DB::table('foothbath_ceklis')
                                        ->where('id_lokasi', $selectedArea)
                                        ->where('id_frekuensi', $row->id)
                                        ->whereMonth('tgl', $selectedBulan)
                                        ->whereDay('tgl', $i)
                                        ->first();

                                    $cekSama = !empty($firstSanitasi);
                                    $cekStatus = !empty($firstSanitasi)
                                        ? ($firstSanitasi->status == 1
                                            ? '√'
                                            : 'x')
                                        : '';
                                @endphp
                                <td x-data="{
                                    handleMouseClick($event) {
                                        if ($event.button === 0) {
                                            $wire.tbhSanitasi(
                                                @json($firstSanitasi?->id),
                                                {{ $row->id }},
                                                '{{ "$tahun-$selectedBulan-$i" }}',
                                                'kiri'
                                            )
                                        } else if ($event.button === 2) {
                                            $event.preventDefault();
                                            $wire.tbhSanitasi(
                                                @json($firstSanitasi?->id),
                                                {{ $row->id }},
                                                '{{ "$tahun-$selectedBulan-$i" }}',
                                                'kanan'
                                            )
                                        }
                                    }
                                }" @mouseup="handleMouseClick($event)" @contextmenu.prevent
                                    class="pointer">
                                    <center>
                                        <input @checked($cekSama) class="form-check-input" type="checkbox" />
                                        @if ($cekStatus)
                                            <br>
                                            {{ $cekStatus }}
                                        @endif
                                    </center>
                                </td>
                            @endfor
                        </tr>
                    @endforeach

                    <tr>
                        <td>Paraf Petugas</td>
                        <td>Ttd</td>
                        <td></td>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <td class="pointer">
                                <x-theme.dropdown-paraf tbl="foothbath_ceklis" :items="$adminSanitasi['petugas']" type="paraf_petugas"
                                    :date="$tahun . '-' . $selectedBulan . '-' . $i" :selectedArea="$selectedArea" :selectedBulan="$selectedBulan" :day="$i" />
                            </td>
                        @endfor
                    </tr>

                    <tr>
                        <td>Verifikator</td>
                        <td>Ttd</td>
                        <td></td>

                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <td>
                                <x-theme.dropdown-paraf tbl="foothbath_ceklis" :items="$adminSanitasi['verifikator']" type="verifikator"
                                    :date="$tahun . '-' . $selectedBulan . '-' . $i" :selectedArea="$selectedArea" :selectedBulan="$selectedBulan" :day="$i" />
                            </td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>
