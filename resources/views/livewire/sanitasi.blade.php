<div>
    <div class="container">
        <h5>Checklist Sanitasi</h5>
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
            <button wire:loading wire:target='tbhSanitasi,tbhParaf,addRow,selectedArea,selectedBulan'
                class="btn btn-secondary btn-sm" type="button" disabled="">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Processing...
            </button>
            <x-theme.alert />

            <div class="table-responsive" style="height: 120vh;">
            <table class="table table-bordered ">
                <thead style="position: sticky; top: 0; z-index: 1; background-color: white;" class=" text-white">
                    <tr>
                        <th class="dhead text-center">Item Pembersihan</th>
                        <th class="dhead text-center">Ttl</th>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <th class="dhead text-center">{{ $i }}</th>
                        @endfor
                    </tr>

                </thead>
                <tbody>
                    @foreach ($itemSanitasi as $d)
                        <tr>
                            <td>{{ $d->nama_item . $d->no_identifikasi }}</td>
                            <td class="text-center">{{ $d->ttl }}</td>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                @php
                                    $firstSanitasi = DB::table('sanitasi')
                                        ->where('id_lokasi', $this->selectedArea)
                                        ->where('id_item', $d->id_item)
                                        ->whereMonth('tgl', $selectedBulan)
                                        ->whereDay('tgl', $i)
                                        ->first();
                                    $cekSama = empty($firstSanitasi) ? false : true;
                                @endphp
                                <td class="pointer"
                                    wire:click="tbhSanitasi({{ $firstSanitasi->id_sanitasi ?? 0 }},{{ $d->id_item }}, '{{ "$tahun-$selectedBulan-$i" }}')">
                                    <center>
                                        <input @checked($cekSama) class="form-check-input" type="checkbox" />
                                    </center>
                                </td>
                            @endfor
                        </tr>
                    @endforeach

                    {{-- ini tambah item pemberrsihan --}}
                    <form wire:submit.prevent="tbhItem">
                        @foreach ($items as $index => $item)
                            <tr>
                                <td colspan="5">
                                    <input type="text" wire:model.live="items.{{ $index }}.name"
                                        class="form-control" placeholder="Item pembersihan">
                                </td>
                                <td>
                                    <button type="button" wire:click="removeRow({{ $index }})"
                                        class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                        <tr x-show="$wire.items.length">
                            <td colspan="3">
                                <button type="submit" class="btn-block btn btn-sm btn-outline-success">Simpan Item
                                    baru</button>
                            </td>
                        </tr>
                      
                    </form>


                    {{-- button tambah baris --}}
                    {{-- <tr>
                        <td colspan="{{ $daysInMonth + 1 }}">
                            <button wire:click="addRow" type="button" class="btn btn-sm btn-outline-primary btn-block">
                                Tambah Baris
                            </button>
                        </td>
                    </tr> --}}
                    {{-- verifikator dan petugas --}}
                    <tr>
                        <td colspan="{{ $daysInMonth + 1 }}"></td>
                    </tr>
                    <tr>
                        <td>Paraf Petugas</td>
                        <td></td>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <td class="pointer">
                                <x-theme.dropdown-paraf tbl="sanitasi" :items="$adminSanitasi['petugas']" type="paraf_petugas"
                                    :date="$tahun . '-' . $selectedBulan . '-' . $i" :selectedArea="$selectedArea" :selectedBulan="$selectedBulan" :day="$i" />
                            </td>
                        @endfor
                    </tr>

                    <tr>
                        <td>Verifikator</td>
                        <td></td>

                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <td>
                                <x-theme.dropdown-paraf tbl="sanitasi" :items="$adminSanitasi['verifikator']" type="verifikator"
                                    :date="$tahun . '-' . $selectedBulan . '-' . $i" :selectedArea="$selectedArea" :selectedBulan="$selectedBulan" :day="$i" />
                            </td>
                        @endfor
                    </tr>

                </tbody>
            </table>
            </div>
        </div>

    </div>
</div>
