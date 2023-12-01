<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button idModal="tambah" modal="Y" href="#" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <a href="{{ route('hariandll.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>
        {{-- <x-theme.btn_filter /> --}}
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="tableHalaman">
                <thead>
                    <tr>
                        <th class="dhead">#</th>
                        <th class="dhead">Bulan dibayar</th>
                        <th class="dhead">Tanggal</th>
                        <th class="dhead">Nama Anak</th>
                        <th class="dhead">Keterangan</th>
                        <th class="dhead">Lokasi</th>
                        @php
                            $ttlRp = 0;
                            foreach ($datas as $d) {
                                $ttlRp += $d->rupiah;
                            }
                        @endphp
                        <th class="text-end dhead">Rupiah <br> (Rp {{ number_format($ttlRp, 0) }})</th>
                        <th class="dhead">
                            @php
                                $adaDitutup = DB::table('tb_hariandll')
                                    ->where('ditutup', 'T')
                                    ->first();
                            @endphp
                            @if (!empty($adaDitutup))
                                <input style="text-align: center" type="checkbox" class="form-check" id="cekSemuaTutup">
                            @endif
                            <br>
                            <span class="badge bg-danger btn_tutup d-none" tipe="tutup" style="cursor: pointer"><i
                                    class="fas fa-check"></i> Tutup </span>
                            <span class="badge bg-danger btn_tutup d-none mt-3" tipe="edit"
                                style="cursor: pointer">Edit</span>
                            {{-- <x-theme.button href="#" icon="fa-check" variant="danger" addClass="btn_tutup"
                                teks="Tutup" /> --}}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ !empty($d->bulan_dibayar) ? date('M y', strtotime('01-' . $d->bulan_dibayar . '-' . date('Y'))) : '' }}
                            <td>{{ tanggal($d->tgl) }}</td>
                            <td>{{ $d->nama }}</td>
                            <td>{{ $d->ket }}</td>
                            <td>{{ $d->lokasi }}</td>
                            <td class="text-end">{{ number_format($d->rupiah, 0) }}</td>
                            <td>
                                @if ($d->ditutup != 'Y')
                                    <input type="checkbox" class="form-check cekTutup" name="cekTutup[]"
                                        id_cabut="{{ $d->id_hariandll }}">
                                @endif
                                {{-- <x-theme.button modal="Y" idModal="delete" data="no_nota={{ $d->id_hariandll }}"
                                    icon="fa-trash" addClass="float-end delete_nota" teks="" variant="danger" /> --}}
                                {{-- <x-theme.button modal="Y" idModal="edit" icon="fa-pen"
                                    addClass="me-1 float-end edit-btn" teks=""
                                    data="id_hariandll={{ $d->id_hariandll }}" /> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <form action="{{ route('hariandll.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="Tambah Harian DLL" size="modal-lg" btnSave="Y">


                <div class="row">
                    <div class="col-lg-4">
                        <table class="table table-striped" width="50%">
                            <thead>
                                <tr>
                                    <th class="dhead">Bulan dibayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select required name="bulan_dibayar" class="form-control select2"
                                            id="">
                                            <option value="">- Pilih -</option>
                                            @foreach ($bulan as $b)
                                                <option value="{{ $b->bulan }}">{{ strtoupper($b->nm_bulan) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="text-warning mt-5"><em>Bulan dibayar harus dipilih</em></h6>
                    </div>
                </div>
                <table class="table table-striped" x-data="{}">
                    <thead>
                        <tr>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead" width="20%">Nama Anak</th>
                            <th class="dhead">Keterangan</th>
                            <th class="dhead">Lokasi</th>
                            <th class="dhead text-end">Rupiah</th>
                            <th class="dhead"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>

                            <td>
                                <input style="font-size: 13px;" type="date" value="{{ date('Y-m-d') }}"
                                    class="form-control" name="tgl[]">
                            </td>
                            <td>
                                <select required name="id_anak[]" class="form-control select2" id="">
                                    <option value="">- Pilih Anak -</option>
                                    @foreach ($anak as $d)
                                        <option value="{{ $d->id_anak }}">{{ strtoupper($d->nama) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="ket[]">
                            </td>
                            <td>
                                <select name="lokasi[]" id="" class="form-control" required>
                                    <option value="">- Pilih Lokasi -</option>
                                    @php
                                        $lokasi = ['resto', 'aga', 'orchad', 'agrilaras'];
                                    @endphp
                                    @foreach ($lokasi as $d)
                                        <option value="{{ strtoupper($d) }}">{{ strtoupper($d) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rupiah[]">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody id="tbh_baris">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="9">
                                <button type="button" class="btn btn-block btn-lg tbh_baris"
                                    style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                                    <i class="fas fa-plus"></i> Tambah Baris Baru
                                </button>
                            </th>
                        </tr>
                    </tfoot>
                </table>

            </x-theme.modal>
        </form>

        <form action="{{ route('hariandll.update') }}" method="post">
            @csrf
            <x-theme.modal idModal="edit" title="Edit Harian DLL" size="modal-lg" btnSave="Y">
                <div id="editBody"></div>
            </x-theme.modal>
        </form>
        <x-theme.btn_alert_delete route="hariandll.delete" name="urutan" :tgl1="$tgl1" :tgl2="$tgl2" />
        @section('js')
            <script>
                $('#tableHalaman').DataTable({
                    "searching": true,
                    scrollY: '400px',
                    scrollX: true,
                    scrollCollapse: true,
                    "autoWidth": false,
                    "paging": false,
                    "ordering": false
                });
                inputChecked('cekSemuaTutup', 'cekTutup')
                $('.btn_tutup').hide(); // Menampilkan tombol jika checkbox dicentang
                $(document).on('change', '.cekTutup, #cekSemuaTutup', function() {
                    $('.btn_tutup').removeClass('d-none');

                    $('.btn_tutup').toggle(this.checked);
                })
                plusRow(1, 'tbh_baris', "hariandll/tbh_baris")
                $(document).on('click', '.btn_tutup', function() {
                    var tipe = $(this).attr('tipe')
                    var selectedRows = [];
                    // Loop melalui semua checkbox yang memiliki atribut 'name="cek[]"'
                    $('input[name="cekTutup[]"]:checked').each(function() {
                        // Ambil ID anak dari atribut 'data-id' atau atribut lain yang sesuai dengan data Anda

                        // Mengambil ID dari kolom pertama (kolom #)
                        var anakId = $(this).attr('id_cabut');

                        // Tambahkan ID anak ke dalam array
                        selectedRows.push(anakId);
                    });
                    if (tipe == 'edit') {
                        $('#edit').modal('show')
                        $.ajax({
                            type: "GET",
                            url: "{{ route('hariandll.edit') }}",
                            data: {
                                id: selectedRows
                            },
                            success: function(response) {
                                $("#editBody").html(response);
                                alertToast('sukses', 'Berhasil save')
                            }
                        });
                    } else {
                        if (confirm('Apakah anda yakin ?')) {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('hariandll.delete') }}",
                                data: {
                                    id: selectedRows,
                                },
                                success: function(r) {
                                    window.location.reload()
                                    alertToast('sukses', 'Berhasil save')
                                }
                            });
                        }
                    }


                })

                $(document).on('click', '.edit', function() {})
                // detail('edit-btn', 'id_hariandll', 'hariandll/edit_load', 'editBody')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
