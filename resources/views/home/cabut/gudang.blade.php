<x-theme.app title="{{ $title }}" table="T" cont="container-fluid">
    <x-slot name="slot">
        <div x-data="app">
            <div class="d-flex justify-content-between mb-3">
                <h6>{{ $title }}</h6>
                <div>
                    <a class="btn btn-sm btn-primary"
                        href="{{ route('cabut.export_gudang', ['bulan' => $bulan, 'tahun' => $tahun, 'id_user' => $id_user]) }}">
                        <i class="fas fa-print"></i> Export All
                    </a>
                    <x-theme.button href="#" icon="fa-plus" variant="info" modal="Y" idModal="tambah"
                        teks="serah" />
                    <x-theme.button href="{{ route('gudangsarang.invoice') }}" icon="fa-clipboard-list"
                        teks="Po Cetak" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl1" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="{{ $posisi == 1 ? '6' : '5' }}">
                                        ({{ count($bk) }}) Box Stock
                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center {{ $posisi == 1 ? '' : 'd-none' }}">Pemilik</th>
                                    <th class="dhead text-center">No Box </th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">Rp/gr</th>
                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">Total rp</th>
                                </tr>
                                @php
                                    if (!function_exists('ttl')) {
                                        function ttl($tl)
                                        {
                                            return [
                                                'pcs' => array_sum(array_column($tl, 'pcs')),
                                                'gr' => array_sum(array_column($tl, 'gr')),
                                                'hrga_satuan' => array_sum(array_column($tl, 'hrga_satuan')),
                                                'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                                'ttl_rp_cbt' => array_sum(array_column($tl, 'ttl_rp_cbt')),
                                            ];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <th class="dheadstock text-center">Total</th>
                                    <th class="dheadstock text-center {{ $posisi == 1 ? '' : 'd-none' }}"></th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($bk)['pcs'], 0) }}</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($bk)['gr'], 0) }}</th>
                                    <th class="dheadstock text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                        {{ number_format(ttl($bk)['hrga_satuan'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                        {{ number_format(ttl($bk)['ttl_rp'], 0) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bk as $d)
                                    <tr>
                                        <td align="center" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ $d->penerima }}</td>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">{{ $d->pcs }}</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->hrga_satuan, 0) }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->hrga_satuan * $d->gr, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4">
                    <input type="text" id="tbl2input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl2" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="{{ $posisi == 1 ? '6' : '5' }}">
                                        ({{ count($cabut) }}) Box sedang proses
                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center {{ $posisi == 1 ? '' : 'd-none' }}">Penerima</th>
                                    <th class="dhead text-center">No Box</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">Rp/Gr</th>
                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">Total Rp</th>
                                </tr>
                                <tr>
                                    <th class="dheadstock text-center">Total</th>
                                    <th class="dheadstock text-center {{ $posisi == 1 ? '' : 'd-none' }}"></th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabut)['pcs'], 0) }}</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabut)['gr'], 0) }}</th>
                                    <th class="dheadstock text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                        {{ number_format(ttl($cabut)['hrga_satuan'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                        {{ number_format(ttl($cabut)['ttl_rp'], 0) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cabut as $d)
                                    <tr>
                                        <td align="center" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ $d->penerima }}</td>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">{{ $d->pcs }}</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->hrga_satuan, 0) }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->ttl_rp, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4">
                    <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl3" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="{{ $posisi == 1 ? '7' : '6' }}">
                                        ({{ count($cabutSelesai) + count($eoSelesai) }}) Box selesai siap ctk
                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center {{ $posisi == 1 ? '' : 'd-none' }}">Penerima</th>
                                    <th class="dhead text-center">No Box</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">Total Rp Bk</th>
                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">Total Rp Cbt</th>
                                    <th class="dhead text-center">Aksi</th>
                                </tr>
                                <tr>
                                    <th class="dheadstock text-center">Total</th>
                                    <th class="dheadstock text-center {{ $posisi == 1 ? '' : 'd-none' }}"></th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(ttl($cabutSelesai)['pcs'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(ttl($cabutSelesai)['gr'] + sumBk($eoSelesai, 'gr'), 0) }}
                                    </th>
                                    <th class="dheadstock text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                        {{ number_format(ttl($cabutSelesai)['ttl_rp'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                        {{ number_format(ttl($cabutSelesai)['ttl_rp_cbt'], 0) }}
                                    </th>
                                    <th class="dheadstock text-center">
                                        <input type="checkbox" x-model="allChecked" @change="toggleAll()"
                                            title="Check All" style="cursor: pointer; width: 18px; height: 18px;">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cabutSelesai as $d)
                                    <tr>
                                        <td align="center" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ $d->pengawas }}</td>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">{{ $d->pcs }}</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->ttl_rp, 0) }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->ttl_rp_cbt, 0) }}</td>
                                        <td align="center">
                                            <input type="checkbox"
                                                @change="tambah('{{ $d->no_box }}', {{ $d->pcs }}, {{ $d->gr }}, {{ $d->ttl_rp_cbt }}, {{ $d->ttl_rp }})"
                                                value="{{ $d->no_box }}" x-model="cek" style="cursor: pointer;">
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($eoSelesai as $d)
                                    <tr>
                                        <td align="center" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ $d->pengawas }}</td>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">0</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->ttl_rp, 0) }}</td>
                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}">
                                            {{ number_format($d->ttl_rp_cbt, 0) }}</td>
                                        <td align="center">
                                            <input type="checkbox"
                                                @change="tambah('{{ $d->no_box }}', 0, {{ $d->gr }}, {{ $d->ttl_rp_cbt }}, {{ $d->ttl_rp }})"
                                                value="{{ $d->no_box }}" x-model="cek" style="cursor: pointer;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- modal ambil box ke cetak --}}
                    <form action="{{ route('cabut.save_formulir') }}" method="post">
                        @csrf
                        <x-theme.modal idModal="tambah" title="Tambah Box" btnSave="Y">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Tgl</label>
                                        <input value="{{ date('Y-m-d') }}" type="date" name="tgl"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <label for="">Pgws Penerima</label>
                                    <select required name="id_penerima" class="form-control select2">
                                        <option value="">- Pilih pgws -</option>
                                        @foreach ($users as $d)
                                            <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="alert alert-info">
                                        <strong>Dipilih: <span x-text="selectedItem.length"></span> Box</strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="dhead">No Box</th>
                                                    <th class="dhead text-end">Pcs</th>
                                                    <th class="dhead text-end">Gr</th>
                                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                                        Total Rp Bk</th>
                                                    <th class="dhead text-end {{ $posisi == 1 ? '' : 'd-none' }}">
                                                        Total Rp Cbt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <input class="d-none" name="no_box[]" type="text"
                                                    :value="cek">
                                                <template x-for="item in selectedItem" :key="item.no_box">
                                                    <tr>
                                                        <td x-text="item.no_box"></td>
                                                        <td align="right" x-text="item.pcs"></td>
                                                        <td align="right" x-text="item.gr"></td>
                                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}"
                                                            x-text="formatNumber(item.ttl_rp)"></td>
                                                        <td align="right" class="{{ $posisi == 1 ? '' : 'd-none' }}"
                                                            x-text="formatNumber(item.ttl_rp_cbt)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </x-theme.modal>
                    </form>
                </div>
            </div>
        </div>

        @section('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('app', () => ({
                        cek: [],
                        selectedItem: [],
                        allChecked: false,

                        // Tambah atau hapus item
                        tambah(no_box, pcs, gr, ttl_rp_cbt, ttl_rp) {

                            // Cek jika sudah ada
                            const index = this.selectedItem.findIndex(item => item.no_box == no_box);

                            if (index === -1) {
                                this.selectedItem.push({
                                    no_box,
                                    pcs: Number(pcs) || 0,
                                    gr: Number(gr) || 0,
                                    ttl_rp: Number(ttl_rp) || 0,
                                    ttl_rp_cbt: Number(ttl_rp_cbt) || 0,
                                });
                                this.cek.push(no_box);
                            } else {
                                this.selectedItem.splice(index, 1);
                                this.cek = this.cek.filter(x => x != no_box);
                            }

                            const total = {{ count($cabutSelesai) + count($eoSelesai) }};
                            this.allChecked = this.selectedItem.length === total;
                        },

                        // CHECK ALL
                        toggleAll() {
                            const checkboxes = document.querySelectorAll('#tbl3 tbody input[type=checkbox]');
                            this.selectedItem = [];
                            this.cek = [];

                            if (this.allChecked) {
                                checkboxes.forEach(cb => {
                                    cb.checked = true;
                                    const tr = cb.closest('tr');

                                    this.tambah(
                                        tr.children[1].innerText.trim(),
                                        tr.children[2].innerText.trim(),
                                        tr.children[3].innerText.trim(),
                                        tr.children[5].innerText.replace(/\./g, '').trim(),
                                        tr.children[4].innerText.replace(/\./g, '').trim(),
                                    );
                                });
                            } else {
                                checkboxes.forEach(cb => cb.checked = false);
                                this.selectedItem = [];
                                this.cek = [];
                            }
                        },

                        formatNumber(value) {
                            return new Intl.NumberFormat('id-ID', {
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    }));
                });
            </script>



            <script>
                ["tbl1", "tbl2", "tbl3"].forEach((tbl, i) => pencarian(`tbl${i+1}input`, tbl));
            </script>
            <script>
                if ({{ $posisi == 1 }}) {
                    document.body.style.zoom = "90%";
                } else {
                    document.body.style.zoom = "75%";
                }
            </script>
        @endsection
    </x-slot>
</x-theme.app>
