<x-theme.app title="{{ $title }}" table="Y" sizeCard="9">
    <x-slot name="cardHeader">
        <h6 class="mt-1">{{ $title }}</h6>
        <br>
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
                <form action="{{ route('kelas.cetakSortir') }}" method="post">
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
                                <th class="dhead" width="100">Kelas</th>
                                <th class="dhead text-end" width="100">Gr</th>
                                <th class="text-end dhead" width="150">Rupiah</th>
                                <th class="text-end dhead">Denda Sst</th>
                                <th class="text-end dhead">Denda Rp</th>
                                <th class="dhead" width="60">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr class="bg-info">
                                <td></td>
                              

                                <td>
                                    <input type="text" name="kelas_tambah[]" class="form-control">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" type="text" name="gr_tambah[]" class="form-control text-end">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" type="text" name="rupiah_tambah[]" class="form-control text-end">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" type="text" name="denda_susut_tambah[]" class="form-control text-end">
                                </td>
                                <td>
                                    <input x-mask:dynamic="$money($input)" type="text" name="denda_tambah[]" class="form-control text-end">
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
                                        <input type="text" name="kelas_tambah[]" class="form-control">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" name="gr_tambah[]" class="form-control text-end">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" name="rupiah_tambah[]" class="form-control text-end">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" name="denda_susut_tambah[]" class="form-control text-end">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" name="denda_tambah[]" class="form-control text-end">
                                    </td>
                                    <td>
                                        <span class="badge bg-danger" @click="rows.splice(index, 1)"
                                            style="cursor: pointer"><i class="fas fa-minus"></i></span>
                                    </td>
                                </tr>
                            </template>
                            @foreach ($kelas as $no => $d)
                                <input type="hidden" name="id_kelas[]" value="{{ $d->id_kelas }}">
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    

                                    <td>
                                        <input type="text" value="{{ $d->kelas }}" name="kelas[]"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" value="{{ $d->gr }}" name="gr[]"
                                            class="form-control text-end">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" value="{{ $d->rupiah }}" name="rupiah[]"
                                            class="form-control text-end">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" value="{{ $d->denda_susut }}" name="denda_susut[]"
                                            class="form-control text-end">
                                    </td>
                                    <td>
                                        <input x-mask:dynamic="$money($input)" type="text" value="{{ $d->denda }}" name="denda[]"
                                            class="form-control text-end">
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
                            url: "{{ route('kelas.deleteSortir') }}",
                            data: {
                                datas: selectedRows
                            },
                            success: function(r) {
                                window.location.reload();
                            }
                        });
                    }

                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
