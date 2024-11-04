<x-theme.app title="{{ $title }}" table="T" cont="container-fluid">
    <x-slot name="slot">
        <div class="p-2" x-data="{
            cek: [],
            cekPrint: [],
            selectedItem: [],
            tambah(id_anak) {
                const selectedItem = this.selectedItem
                const index = selectedItem.findIndex(item => item.id_anak === id_anak);
                if (index === -1) {
                    selectedItem.push({
                        id_anak: id_anak,
                    });
                } else {
                    selectedItem.splice(index, 1);
                }
        
            },
        }">

            <div class="d-flex justify-content-between mb-3 gap-1">
                <h6>{{ $title }}</h6>
                <div class="d-flex gap-1">
                    <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" />
                    <div>
                        <form action="{{ route('pengawas.create_invoice') }}" method="get">
                            <input type="hidden" name="id_anak" class="form-control" :value="cek.join(',')">
                            <button value="berhenti" x-transition x-show="cek.length" class="btn btn-sm btn-danger"
                                name="submit">
                                Berhenti
                                <span class="badge bg-white text-black" x-text="cek.length + ' Anak'"
                                    x-transition></span>
                            </button>
                            <button value="bayar" x-transition x-show="cek.length" class="btn btn-sm btn-primary"
                                name="submit">
                                Bayar
                                <span class="badge bg-white text-black" x-text="cek.length + ' Anak'"
                                    x-transition></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <section class="row">
                <table class="table" id="nanda">
                    <thead>
                        <tr>
                            <th width="5">#</th>
                            <th>Tgl Masuk</th>
                            <th>Tgl Dibayar</th>
                            <th>Nama Karyawan</th>
                            <th>Kelas</th>
                            <th>Pembawa</th>
                            <th>Pengawas</th>
                            <th>Keterangan</th>
                            <th>Periode</th>
                            <th class="text-end">Komisi Rp</th>
                            <th>Tgl Lunas</th>
                            <th>Pembayar</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $no => $d)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ tanggal($d->tgl_masuk) }}</td>
                                <td>{{ !$d->tgl_dibayar ? '' : tanggal($d->tgl_dibayar) }}</td>
                                <td>{{ ucwords($d->nama) }}</td>
                                <td>{{ ucwords($d->id_kelas) }}</td>
                                <td>{{ ucwords($d->pembawa) }}</td>
                                <td>{{ ucwords($d->name) }}</td>
                                <td>{{ 'dibayar ' . number_format($d->komisi, 0) }}</td>
                                <td>{{ "$d->periode Bulan" }}</td>
                                <td align="right">{{ number_format($d->komisi, 0) }}</td>
                                <td>{{ !empty($d->tgl_lunas) ? tanggal($d->tgl_lunas) : '-' }}</td>
                                <td>
                                    <a target="_blank"
                                        href="{{ route('pengawas.invoice', ['no_invoice' => $d->no_invoice]) }}">
                                        {{ $d->pembayar ? $d->no_invoice . ' ' . $d->pembayar : '-' }}
                                    </a>
                                </td>
                                <td>
                                    @if ($d->berhenti == 'Y')
                                        <span class="badge bg-danger">
                                            Berhenti
                                        </span>
                                    @endif
                                    @if ($d->pembayar)
                                        <span class="badge bg-success">
                                            Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="d-flex gap-3 align-middle justify-content-center">
                                    {{-- <a onclick="return confirm('Yakin dihapus ?')" class="btn btn-sm btn-danger float-end" href="{{ route('pengawas.destroy_anak', $d->id_anak) }}"><i class="fas fa-trash"></i></a> --}}
                                    <div>

                                        <x-theme.button modal="Y" idModal="edit" href="#" icon="fa-pen"
                                            addClass="float-end edit" teks="" data="id={{ $d->id_anak }}" />
                                    </div>
                                    <div>
                                        @if ($d->berhenti == 'Y' || $d->pembayar)
                                            x
                                        @else
                                            <input type="checkbox" @change="tambah({{ $d->id_anak }})"
                                                value="{{ $d->id_anak }}" class="pointer" x-model="cek">
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        {{-- ALL MODAL --}}
        <form action="{{ route('pengawas.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah user" btnSave="Y">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Nama Anak</label>
                            <input required type="text" name="nama" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Kelas</label>
                            <select name="kelas" class="select2" id="">
                                <option value="">Kelas</option>
                                @php
                                    $kelas = [1, 2, 3];
                                @endphp
                                @foreach ($kelas as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Pgws Pembawa</label>
                            <input required type="text" id="pembawa" placeholder="pembawa karyawan" name="pembawa"
                                class="form-control">
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Pengawas Cbt</label>
                            <select name="id_pengawas" id="" class="select2">
                                <option value="">- Pilih Pengawas -</option>
                                @foreach ($pengawas as $p)
                                    <option {{ $p->id == auth()->user()->id ? 'selected' : '' }}
                                        value="{{ $p->id }}">{{ ucwords($p->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Tgl Masuk</label>
                            <input required type="date" id="tgl_masuk" value="{{ date('Y-m-d') }}" name="tgl_masuk"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Periode Bulan Bayar</label>
                            <select required name="periode" id="periode" class="select3">
                                <option value="">- Periode -</option>
                                @for ($i = 1; $i < 13; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Komisi Rp</label>
                            <input required type="text" value="" name="komisi" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Tgl Dibayar</label>
                            <input readonly id="tgl_dibayar" required type="date" value="{{ date('Y-m-d') }}"
                                name="tgl_dibayar" class="form-control">
                        </div>
                    </div>


                </div>
            </x-theme.modal>
        </form>

        {{-- update --}}
        <form action="{{ route('pengawas.update_anak') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Anak" idModal="edit">
                <div id="get_edit">
                </div>
            </x-theme.modal>
        </form>
        </div>
    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {
                $(".select3").select2()
                detailEdit('edit', 'id', 'anak', 'get_edit')
                tglBayar('periode', 'tgl_masuk', 'tgl_dibayar')

                function detailEdit(kelas, attr, link, load) {
                    $(document).on('click', `.${kelas}`, function() {
                        var id = $(this).attr(`${attr}`)
                        alert(id)
                        $.ajax({
                            type: "GET",
                            url: `${link}/${id}`,
                            success: function(r) {
                                $(`#${load}`).html(r);
                                $('.select2-edit').select2({
                                    dropdownParent: $('#edit .modal-content')
                                });
                                $('#tableDetail').DataTable({
                                    "paging": true,
                                    "pageLength": 10,
                                    "lengthChange": true,
                                    "stateSave": true,
                                    "searching": true,
                                });
                                tglBayar('periode' + id  , 'tgl_masuk' + id, 'tgl_dibayar' + id)
                            }
                        });
                    })
                }

                function tglBayar(periode,tgl_masuk,tgl_dibayar) {
                    $(document).on('change', `#${periode}, #${tgl_masuk}`, function() {
                        let tglMasuk = $(`#${tgl_masuk}`).val();
                        let periode = parseInt($(`#${periode}`).val());

                        // Cek apakah kedua input memiliki nilai yang valid
                        if (tglMasuk && periode) {
                            let tgl = new Date(tglMasuk);
                            tgl.setMonth(tgl.getMonth() + periode);

                            // Format tanggal menjadi YYYY-MM-DD
                            let tglDibayar = tgl.toISOString().split('T')[0];
                            $(`#${tgl_dibayar}`).val(tglDibayar);
                        }
                    })
                  
                }
            });
        </script>
    @endsection
</x-theme.app>
