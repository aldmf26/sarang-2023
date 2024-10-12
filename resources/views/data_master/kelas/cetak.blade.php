<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        @include('data_master.kelas.nav')
   

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
            <div class="col-lg-10">
                <form action="{{ route('kelas.cetakCreate') }}" method="post">
                    <x-theme.button href="#" icon="fa-window-close" variant="danger"
                        addClass="float-end btn_tutup" teks="Hapus" />
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
                                <th class="dhead" width="100">Paket</th>
                                <th class="dhead" width="100">Kelas</th>
                                <th class="dhead" width="100">Tipe</th>
                                <th class="text-end dhead" width="150">Rp Pcs</th>
                                <th class="text-end dhead">Denda HCR</th>
                                <th class="text-end dhead">Bts Sst %</th>
                                <th class="text-end dhead">Denda Sst</th>
                                <th class="text-end dhead">Rp Gaji</th>
                                <th class="dhead" width="60">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr class="bg-info">
                                <td></td>
                                <td>
                                    <select database="paket" x-model="selectedOption" x-init="initSelect2()"
                                        class="select2-alpine" name="id_paket_tambah[]" id="">
                                    </select>
                                </td>

                                <td>
                                    <input type="text" name="kelas_tambah[]" class="form-control">
                                </td>
                                <td>
                                    <select database="tipe" x-model="selectedOption" x-init="initSelect2()"
                                        class="select2-alpine" name="id_tipe_brg_tambah[]" id="">

                                    </select>
                                </td>

                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="rupiah_tambah[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="denda_hcr[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="batas_susut[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="denda_susut[]">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                        name="rp_gaji[]">
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
                                        <select database="paket" x-model="selectedOption" x-init="initSelect2()"
                                            class="select2-alpine" name="id_paket_tambah[]" id="">
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
                                        <select database="tipe" x-model="selectedOption" x-init="initSelect2()"
                                            class="select2-alpine" name="id_tipe_brg_tambah[]" id="">
                                            <option value="">Tipe</option>
                                            @foreach ($tipe as $t)
                                                <option value="{{ $t->id_tipe }}">{{ strtoupper($t->tipe) }}</option>
                                            @endforeach

                                        </select>
                                    </td>

                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="rupiah_tambah[]">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="denda_hcr[]">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="batas_susut[]">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="denda_susut[]">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="rp_gaji[]">
                                    </td>
                                    <td>
                                        <span class="badge bg-danger" @click="rows.splice(index, 1)"
                                            style="cursor: pointer"><i class="fas fa-minus"></i></span>
                                    </td>
                                </tr>
                            </template>
                            @foreach ($kelas as $no => $d)
                                <input type="hidden" name="id_kelas_cetak[]" value="{{ $d->id_kelas_cetak }}">
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    <td>
                                        <select database="paket" x-data="{ selectedTipeKategori: {{ $d->id_paket }} }"
                                            x-model="selectedTipeKategori" x-init="initSelect2()"
                                            class="select2-alpine-data" name="id_paket[]" id="">
                                            @foreach ($kategori as $t)
                                                <option
                                                    x-bind:selected="selectedTipeKategori == $t - > id_paket ? true : false"
                                                    value="{{ $t->id_paket }}">{{ strtoupper($t->paket) }}</option>
                                            @endforeach
                                            <option value="tambah">+ Paket</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="text" value="{{ $d->kelas }}" name="kelas[]"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <select database="tipe" x-data="{ selectedTipe: {{ $d->tipe }} }" x-model="selectedTipe"
                                            x-init="initSelect2()" class="select2-alpine-data" name="id_tipe_brg[]"
                                            id="">
                                            @foreach ($tipe as $t)
                                                <option x-bind:selected="selectedTipe == $t - > id_tipe ? true : false"
                                                    value="{{ $t->id_tipe }}">{{ strtoupper($t->tipe) }}</option>
                                            @endforeach
                                            <option value="tambah">+ Tipe</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input x-mask:dynamic=" $money($input)" value="{{ $d->rp_pcs }}"
                                            class="form-control text-end" name="rupiah[]">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="denda_hcr[]" value="{{ $d->denda_hcr }}">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="batas_susut[]" value="{{ $d->batas_susut }}">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="denda_susut[]" value="{{ $d->denda_susut }}">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                                            name="rp_gaji[]" value="{{ $d->rp_gaji }}">
                                    </td>
                                    <td>
                                        <span id_kelas="{{ $d->id_kelas_cetak }}" data-bs-target="#infoKelas"
                                            data-bs-toggle="modal" class="badge bg-primary infoKelas"
                                            style="cursor: pointer"><i class="fas fa-question"></i></span>
                                        <input type="checkbox" class="cekTutup float-end" name="cekTutup[]"
                                            id_kelas="{{ $d->id_kelas_cetak }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </form>
            </div>
            <x-theme.modal idModal="infoKelas" title="Info Rules" btnSave="T" size="modal-lg">
                <div id="infoBody"></div>
            </x-theme.modal>

            <form id="formTambahSelect">
                <x-theme.modal idModal="tambahSelect" title="Tambah" btnSave="Y" size="modal-sm">
                    <input type="text" class="form-control" name="ket">
                    <input type="hidden" class="form-control database" name="database" value="0">
                </x-theme.modal>
            </form>
        </section>


        @section('scripts')
            <script>
                // select
                loadTipe()

                function loadTipe() {
                    $.ajax({
                        url: "{{ route('kelas.getTipe') }}?database=paket", // Ganti dengan URL endpoint yang sesuai
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response)
                            // Mengisi opsi select dengan data yang diterima dari server
                            var $select = $('.select2-alpine[database="paket"]');
                            $select.empty();
                            $select.append($('<option>', {
                                value: '',
                                text: 'Paket'
                            }));
                            $.each(response, function(index, paket) {
                                $select.append($('<option>', {
                                    value: paket.id_paket,
                                    text: paket.paket
                                }));
                            });
                            $select.append($('<option>', {
                                value: 'tambah',
                                text: '+ Paket'
                            }));
                            // Inisialisasi ulang select2 setelah mengisi opsi
                            initSelect2();
                        },
                        error: function(xhr, status, error) {
                            console.error('Terjadi kesalahan dalam memuat opsi tipe: ' + error);
                        }
                    });
                    $.ajax({
                        url: "{{ route('kelas.getTipe') }}?database=tipe", // Ganti dengan URL endpoint yang sesuai
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response)
                            // Mengisi opsi select dengan data yang diterima dari server
                            var $select = $('.select2-alpine[database="tipe"]');
                            $select.empty();
                            $select.append($('<option>', {
                                value: '',
                                text: 'Tipe'
                            }));
                            $.each(response, function(index, tipe) {
                                $select.append($('<option>', {
                                    value: tipe.id_tipe,
                                    text: tipe.tipe
                                }));
                            });
                            $select.append($('<option>', {
                                value: 'tambah',
                                text: '+ Tipe'
                            }));
                            // Inisialisasi ulang select2 setelah mengisi opsi
                            initSelect2();
                        },
                        error: function(xhr, status, error) {
                            console.error('Terjadi kesalahan dalam memuat opsi tipe: ' + error);
                        }
                    });
                }
                changeSelect2('select2-alpine')
                changeSelect2('select2-alpine-data')

                function changeSelect2(kelas) {
                    $(document).on('change', '.' + kelas, function() {
                        var nilai = $(this).val()
                        var db = $(this).attr('database')
                        $('.database').val(db);
                        if (nilai === 'tambah') {
                            $('#tambahSelect').modal('show')
                            $(document).on('submit', '#formTambahSelect', function(e) {
                                e.preventDefault();

                                var datas = $(this).serialize()
                                $.ajax({
                                    type: "GET",
                                    url: "{{ route('kelas.tambahPaketSelect2') }}",
                                    data: datas,
                                    dataType: 'json',
                                    success: function(r) {
                                        if (kelas === 'select2-alpine-data') {
                                            window.location.reload()
                                        }
                                        loadTipe()
                                        $('#tambahSelect').modal('hide')

                                    }
                                });
                            })
                        }
                    })
                }

                // end select

                detail('infoKelas', 'id_kelas', 'info', 'infoBody')
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
