<div>
    <div class="row">
        <div class="col-lg-3">
            <div class="mb-3">
                <label for="bulan" class="form-label">Pilih Ruangan</label>
                <input type="text" wire:model.change="selectedRuangan" class="form-control">
            </div>
        </div>
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
                <label for="jenisLimbah" class="form-label">Standard Suhu {{ $adaData }}</label>
                <input type="text" wire:model.change="selectedStandardSuhu" class="form-control">
            </div>
        </div>
        @if ($adaData == 'tidak')
            <div class="col-lg-3" wire:transition>
                <label for="">Aksi</label> <br>
                <button wire:click='store' class="btn btn-sm btn-success" type="button">Tambah Baru</button>
            </div>
        @endif

        <div class="col-lg-12">
            <button wire:loading wire:target='selectedBulan,selectedRuangan,selectedStandardSuhu,ubah'
                class="mb-2 btn btn-secondary btn-sm" type="button" disabled="">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Processing...
            </button>
            @if ($adaData == 'ada')
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center dhead">Tanggal</th>
                            <th class="text-center dhead">Suhu</th>
                            <th class="text-center dhead">pemeriksa</th>
                            <th class="text-center dhead">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $suhuVal = $inputValues[$selectedBulan][$i]['suhu'] ?? '';
                                $pemeriksaVal = $inputValues[$selectedBulan][$i]['pemeriksa'] ?? '';
                                $ketVal = $inputValues[$selectedBulan][$i]['ket'] ?? '';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }} </td>
                                <td>
                                    <input wire:change="ubah('suhu',{{ $i }},$event.target.value)"
                                        value="{{ $suhuVal }}" @click="klik2xEdit($event)" @readonly($suhuVal)
                                        type="text" class="form-control form-control-sm" placeholder="suhu">
                                </td>
                                <td>
                                    <input wire:change="ubah('pemeriksa',{{ $i }},$event.target.value)"
                                        value="{{ $pemeriksaVal }}" @click="klik2xEdit($event)" @readonly($pemeriksaVal)
                                        type="text" class="form-control form-control-sm" placeholder="pemeriksa">
                                </td>
                                <td>
                                    <input wire:change="ubah('ket',{{ $i }},$event.target.value)"
                                        value="{{ $ketVal }}" @click="klik2xEdit($event)" @readonly($ketVal)
                                        type="text" class="form-control form-control-sm" placeholder="ket">
                                </td>
                            </tr>
                        @endfor

                    </tbody>
                </table>
            @else
                <h6 class="text-warning">Data tidak ditemukan, Ruangan / Standard Suhu tidak cocok</h6>
            @endif
        </div>
    </div>
</div>

<script>
    function klik2xEdit(event) {
        if (event.detail === 2) {
            event.target.removeAttribute('readonly');
            const targetInput = event.target;

            document.addEventListener('click', function handler(e) {
                // Jika klik di luar input, kembalikan ke readonly
                if (!targetInput.contains(e.target)) {
                    targetInput.setAttribute('readonly', true);
                    // Hapus event listener setelah selesai
                    document.removeEventListener('click', handler);
                }
            });
        }
    }
</script>
