<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h3 class="float-start mt-1">{{ $title }}</h3>
        <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Nama</th>
                        <th>Tgl Masuk</th>
                        <th>Kelas</th>
                        <th>Pengawas</th>
                        <th>Uang Makan</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ ucwords($d->nama) }}</td>
                            <td>{{ tanggal($d->tgl_masuk) }}</td>
                            <td>{{ ucwords($d->id_kelas) }}</td>
                            <td>{{ ucwords($d->name) }}</td>
                            <td class="text-end">Rp. {{ number_format($d->nominal, 0) }}</td>
                            <td>
                                {{-- <a onclick="return confirm('Yakin dihapus ?')" class="btn btn-sm btn-danger float-end" href="{{ route('pengawas.destroy_anak', $d->id_anak) }}"><i class="fas fa-trash"></i></a> --}}

                                <x-theme.button modal="Y" idModal="edit" href="#" icon="fa-pen"
                                    addClass="float-end edit" teks="" data="id={{ $d->id_anak }}" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- ALL MODAL --}}
        <form action="{{ route('pengawas.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah user" btnSave="Y">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            <label for="">Nama Anak</label>
                            <input required type="text" name="nama" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-3">
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
                            <label for="">Tgl Masuk</label>
                            <input required type="date" value="{{ date('Y-m-d') }}" name="tgl_masuk"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Pengawas</label>
                            <select name="id_pengawas" id="" class="select2">
                                <option value="">- Pilih Pengawas -</option>
                                @foreach ($pengawas as $p)
                                    <option value="{{ $p->id }}">{{ ucwords($p->name) }}</option>
                                @endforeach
                            </select>
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

        {{-- delete --}}

    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {
                $(".select3").select2()
                detail('edit', 'id', 'anak', 'get_edit')
            });
        </script>
    @endsection
</x-theme.app>
