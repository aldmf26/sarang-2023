<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">
                {{-- <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" /> --}}
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>

            @include('data_master.kelas.nav')
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                font-size: 12px;
                width: auto;
            }
        </style>
        <section class="row">

            <form action="{{ route('kelas.cabutCreate') }}" method="post">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <x-theme.button href="#" icon="fa-window-close" variant="danger" addClass="float-end btn_tutup"
                    teks="Hapus" />
                <button class="btn btn-primary btn-sm float-end mb-3 me-2" type="submit"><i
                        class="fas fa-plus"></i>Simpan</button>
                @csrf
                <table class="table" id="tblScroll" x-data="{
                    rows: [],
                    selectedOption: null
                }">
                    <thead>
                        <tr>
                            <th class="dhead" width="15">#</th>
                            <th class="dhead" width="50">Paket</th>
                            <th class="dhead" width="140">Kelas</th>
                            <th class="dhead" width="80">Tipe</th>
                            @if ($jenis == 1)
                                <th class="text-center dhead" width="70">Pcs</th>
                            @else
                                <th class="text-center dhead" width="70">Gr</th>
                            @endif
                            <th class="text-end dhead" width="100">Rp</th>
                            <th class="text-end dhead">Denda Susut %</th>
                            <th class="text-end dhead">Batas Susut</th>
                            <th class="text-end dhead">Bonus Susut</th>
                            <th class="text-end dhead" width="100">Rp Bonus</th>
                            <th class="text-end dhead">Batas Eot</th>
                            <th class="text-end dhead">Eot</th>
                            <th class="text-end dhead">Denda Hcr</th>
                            <th class="dhead" width="70">Aksi</th>

                        </tr>
                    </thead>
                    <tbody>

                        <tr class="bg-info">
                            <td></td>
                            <td>
                                <select x-model="selectedOption" x-init="initSelect2()" class="select2-alpine"
                                    name="id_kategori_tambah[]" id="">
                                    <option value="">Paket</option>
                                    @foreach ($kategori as $t)
                                        <option value="{{ $t->id_paket }}">{{ strtoupper($t->paket) }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="text" name="kelas_tambah[]" class="form-control">
                            </td>
                            <td>
                                <select x-model="selectedOption" x-init="initSelect2()" class="select2-alpine"
                                    name="id_tipe_brg_tambah[]" id="">
                                    <option value="">Tipe</option>
                                    @foreach ($tipe as $t)
                                        <option value="{{ $t->id_tipe }}">{{ strtoupper($t->tipe) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            @if ($jenis == 1)
                                <td>
                                    <input x-mask:dynamic=" $money($input)" class="form-control text-end"
                                        name="pcs_tambah[]">
                                </td>
                            @else
                                <td>
                                    <input x-mask:dynamic=" $money($input)" class="form-control text-end"
                                        name="gr_tambah[]">
                                </td>
                            @endif
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="rupiah_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="denda_susut_persen_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="batas_susut_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="bonus_susut_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="rp_bonus_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="batas_eot_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="eot_tambah[]">
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                    name="denda_hcr_tambah[]">
                            </td>

                            <td>
                                <span class="badge bg-primary" @click="rows.push({ value: '' })"
                                    style="cursor: pointer"><i class="fas fa-plus"></i></span>
                            </td>

                        </tr>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr class="bg-info">
                                <td></td>
                                <td>
                                    <select x-model="selectedOption" x-init="initSelect2()" class="select2-alpine"
                                        name="id_kategori_tambah[]" id="">
                                        <option value="">Paket</option>
                                        @foreach ($kategori as $t)
                                            <option value="{{ $t->id_paket }}">{{ strtoupper($t->paket) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="text" name="kelas_tambah[]" class="form-control">
                                </td>
                                <td>
                                    <select x-model="selectedOption" x-init="initSelect2()" class="select2-alpine"
                                        name="id_tipe_brg_tambah[]" id="">
                                        <option value="">Tipe</option>
                                        @foreach ($tipe as $t)
                                            <option value="{{ $t->id_tipe }}">{{ strtoupper($t->tipe) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @if ($jenis == 1)
                                    <td>
                                        <input x-mask:dynamic=" $money($input)" class="form-control text-end"
                                            name="pcs_tambah[]">
                                    </td>
                                @else
                                    <td>
                                        <input x-mask:dynamic=" $money($input)" class="form-control text-end"
                                            name="gr_tambah[]">
                                    </td>
                                @endif
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="rupiah_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="denda_susut_persen_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="batas_susut_tambah[]">
                                </td>

                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="bonus_susut_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="rp_bonus_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="batas_eot_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="eot_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="denda_hcr_tambah[]">
                                </td>

                                <td>
                                    <span class="badge bg-danger" @click="rows.splice(index, 1)"
                                        style="cursor: pointer"><i class="fas fa-minus"></i></span>
                                </td>

                            </tr>
                        </template>
                        @foreach ($datas as $no => $d)
                            <input type="hidden" name="id_kelas[]" value="{{ $d->id_kelas }}">
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>
                                    <select x-data="{ selectedTipeKategori: {{ $d->id_kategori }} }" x-model="selectedTipeKategori"
                                        x-init="initSelect2()" class="select2-alpine" name="id_kategori[]"
                                        id="">
                                        @foreach ($kategori as $t)
                                            <option
                                                x-bind:selected="selectedTipeKategori == $t - > id_paket ? true : false"
                                                value="{{ $t->id_paket }}">{{ strtoupper($t->paket) }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="text" value="{{ $d->kelas }}" name="kelas[]"
                                        class="form-control">
                                </td>
                                <td>
                                    <select x-data="{ selectedTipe: {{ $d->id_tipe_brg }} }" x-model="selectedTipe" x-init="initSelect2()"
                                        class="select2-alpine" name="id_tipe_brg[]" id="">
                                        @foreach ($tipe as $t)
                                            <option x-bind:selected="selectedTipe == $t - > id_tipe ? true : false"
                                                value="{{ $t->id_tipe }}">{{ strtoupper($t->tipe) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @if ($jenis == 1)
                                    <td>
                                        <input x-mask:dynamic=" $money($input)" value="{{ $d->pcs }}"
                                            class="form-control text-end" name="pcs[]">
                                    </td>
                                @else
                                    <td>
                                        <input x-mask:dynamic=" $money($input)" value="{{ $d->gr }}"
                                            class="form-control text-end" name="gr[]">
                                    </td>
                                @endif
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->rupiah }}"
                                        class="form-control text-end" name="rupiah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->denda_susut_persen }}"
                                        class="form-control text-end" name="denda_susut_persen[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->batas_susut }}"
                                        class="form-control text-end" name="batas_susut[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->bonus_susut }}"
                                        class="form-control text-end" name="bonus_susut[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->rp_bonus }}"
                                        class="form-control text-end" name="rp_bonus[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->batas_eot }}"
                                        class="form-control text-end" name="batas_eot[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->eot }}"
                                        class="form-control text-end" name="eot[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic=" $money($input)" value="{{ $d->denda_hcr }}"
                                        class="form-control text-end" name="denda_hcr[]">
                                </td>

                                <td>
                                    <span id_kelas="{{ $d->id_kelas }}" data-bs-target="#infoKelas"
                                        data-bs-toggle="modal" class="badge bg-primary infoKelas"
                                        style="cursor: pointer"><i class="fas fa-question"></i></span>
                                    <input type="checkbox" class="cekTutup float-end" name="cekTutup[]"
                                        id_kelas="{{ $d->id_kelas }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </form>

            <x-theme.modal idModal="infoKelas" title="Info Rules" btnSave="T" size="modal-lg">
                <div id="infoBody"></div>
            </x-theme.modal>

        </section>


        @section('scripts')
            <script>
                detail('infoKelas', 'id_kelas', 'kelas/info', 'infoBody')
                $('.btn_tutup').hide(); // Menampilkan tombol jika checkbox dicentang
                $(document).on('change', '.cekTutup', function() {
                    $('.btn_tutup').toggle(this.checked);
                })
                $(document).on('click', '.btn_tutup', function() {
                    if (confirm('Yakin dihapus ?')) {
                        var selectedRows = [];
                        // Loop melalui semua checkbox yang memiliki atribut 'name="cek[]"'
                        $('input[name="cekTutup[]"]:checked').each(function() {
                            // Ambil ID anak dari atribut 'data-id' atau atribut lain yang sesuai dengan data Anda

                            // Mengambil ID dari kolom pertama (kolom #)
                            var anakId = $(this).attr('id_kelas');

                            // Tambahkan ID anak ke dalam array
                            selectedRows.push(anakId);
                        });

                        $.ajax({
                            type: "GET",
                            url: "{{ route('kelas.deleteCabut') }}",
                            data: {
                                datas: selectedRows
                            },
                            success: function(r) {
                                window.location.reload();
                            }
                        });
                    }

                })
                plusRow(1, 'tbh_baris', "hariandll/tbh_baris")
                detail('edit-btn', 'id_hariandll', 'hariandll/edit_load', 'editBody')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
