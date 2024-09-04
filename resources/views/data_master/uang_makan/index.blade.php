<x-theme.app title="{{ $title }}" table="Y" sizeCard="6">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>

            </div>

            <div class="col-lg-6">
                <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" />
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>


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
            <table class="table table-bordered" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center">Aktif</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uang_makan as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td class="text-end">Rp. {{ number_format($d->nominal) }}</td>
                            <td class="text-center">
                                <span
                                    class="badge bg-{{ $d->aktiv == 'Y' ? 'success' : 'danger' }}">{{ $d->aktiv == 'Y' ? 'Aktif' : 'Tidak Aktif' }}</span>

                            </td>
                            <td class="text-center">
                                {{-- <a onclick="return confirm('Yakin dihapus ?')" class="btn btn-sm btn-danger float-end" href="{{ route('pengawas.destroy_anak', $d->id_anak) }}"><i class="fas fa-trash"></i></a> --}}

                                <x-theme.button modal="Y" idModal="edit" href="#" icon="fa-pen"
                                    addClass=" edit" teks="" data="id={{ $d->id_uang_makan }}" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


            <form action="{{ route('uang_makan.tambah_uang_makan') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="Tambah uang makan" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="form-group">
                                <label for="">Nominal</label>
                                <input required type="number" name="nominal" class="form-control">
                            </div>
                        </div>


                    </div>
                </x-theme.modal>
            </form>


            <form action="{{ route('uang_makan.update') }}" method="post">
                @csrf
                <x-theme.modal title="Edit Uang Makan" idModal="edit">
                    <div id="get_edit">
                    </div>
                </x-theme.modal>
            </form>
        </section>


        @section('scripts')
            <script>
                $(document).ready(function() {
                    $(".select3").select2()
                    detail('edit', 'id', 'uang_makan/uang_makan', 'get_edit')
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
