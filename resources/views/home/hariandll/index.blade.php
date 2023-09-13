<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button idModal="tambah" modal="Y" href="#" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <a href="{{ route('hariandll.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm icon icon-left btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nama Anak</th>
                        <th>Keterangan</th>
                        <th>Lokasi</th>
                        <th class="text-end">Rupiah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($d->tgl) }}</td>
                            <td>{{ $d->nama }}</td>
                            <td>{{ $d->ket }}</td>
                            <td>{{ $d->lokasi }}</td>
                            <td class="text-end">{{ number_format($d->rupiah, 0) }}</td>
                            <td>

                                <x-theme.button modal="Y" idModal="delete" data="no_nota={{ $d->id_hariandll }}"
                                    icon="fa-trash" addClass="float-end delete_nota" teks="" variant="danger" />
                                <x-theme.button modal="Y" idModal="edit" icon="fa-pen"
                                    addClass="me-1 float-end edit-btn" teks=""
                                    data="id_hariandll={{ $d->id_hariandll }}" />
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                            name="tgl">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="dhead" width="20%">Nama Anak</th>
                            <th class="dhead text-end">Keterangan</th>
                            <th class="dhead">Lokasi</th>
                            <th class="dhead">Rupiah</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
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
                                <select name="lokasi[]" id="" class="form-control select2">
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
                                <input type="number" min="0" class="form-control" name="rupiah[]">
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
            <x-theme.modal idModal="edit" title="Edit Harian DLL" btnSave="Y">
                <div id="editBody"></div>
            </x-theme.modal>
        </form>
        <x-theme.btn_alert_delete route="hariandll.delete" name="urutan" :tgl1="$tgl1" :tgl2="$tgl2" />
        @section('js')
            <script>
                plusRow(1, 'tbh_baris', "hariandll/tbh_baris")
                detail('edit-btn', 'id_hariandll', 'hariandll/edit_load', 'editBody')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
