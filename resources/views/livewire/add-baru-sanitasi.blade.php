<div>
    <div>
        <div class="container mt-5">
            <h3>Checklist Sanitasi</h3>
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
                        <select required wire:model.live="selectedArea" id="area" class="form-select">
                            <option value="">Pilih area</option>
                            @foreach ($lokasis as $l)
                                <option value="{{ $l->id_lokasi }}">{{ $l->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- Pilih Bulan -->


            <!-- Pilih Area -->
            <!-- Tabel Checklist Sanitasi -->
            <div class="table-responsive">
                <button wire:loading wire:target='tbhSanitasi' class="btn btn-secondary btn-sm" type="button"
                    disabled="">
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    Processing...
                </button>
                <table class="table table-bordered text-center">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Item Pembersihan</th>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                <th>{{ $i }}</th>
                            @endfor
                        </tr>

                    </thead>
                    <tbody>
                        @if (!empty($itemSanitasi))
                            @foreach ($itemSanitasi as $d)
                                <tr>
                                    <td>{{ $d->nama_item }}</td>
                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $firstSanitasi = DB::table('sanitasi')
                                                ->where('id_lokasi', $this->id_lokasi)
                                                ->where('id_item', $d->id_item)
                                                ->whereDay('tgl', $i)
                                                ->first();
                                            $cekSama = empty($firstSanitasi) ? false : true;
                                        @endphp
                                        <td class="pointer"
                                            wire:click="tbhSanitasi({{ $firstSanitasi->id_sanitasi ?? 0 }},{{ $d->id_item }}, '{{ "2025-$selectedBulan-$i" }}')">
                                            <input @checked($cekSama) class="form-check-input"
                                                type="checkbox" />
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        @endif

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
                            <tr>
                                <td colspan="10">
                                    <x-theme.alert />
                                    @if ($openRedirect)
                                        Klik disini untuk pergi ke :
                                        <a target="_blank"
                                            href="{{ route('hrga6_2.create', [
                                                'bulan' => $selectedBulan,
                                                'tahun' => 2025,
                                                'id_lokasi' => $id_lokasi,
                                            ]) }}">{{ formatTglGaji($selectedBulan, 2025) }}</a>
                                    @endif
                                </td>
                            </tr>
                        </form>
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}">
                                <button wire:click="addRow" type="button"
                                    class="btn btn-sm btn-outline-primary btn-block">
                                    Tambah Baris
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>

    </div>

</div>
