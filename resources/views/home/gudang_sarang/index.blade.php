<x-theme.app title="{{ $title }} " table="Y" sizeCard="6" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <div class="col-lg-4">
                <h6 class="">{{ $title }}</h6>
            </div>
            <div class="col-lg-8">
                <x-theme.button modal="Y" idModal="formulir" href="#" icon="fa-plus"
                    addClass="float-end formulir" teks="Formulir" />
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="nanda">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>No Box</th>
                        <th>Nama Anak</th>
                        <th class="text-end">Pcs Akhir</th>
                        <th class="text-end">Gr Akhir</th>
                        <th class="text-center">
                            <input type="checkbox" name="" id="checkAll">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabut as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $d->no_box }}</td>
                            <td>{{ $d->nama }}</td>
                            <td class="text-end">{{ $d->pcs_akhir }}</td>
                            <td class="text-end">{{ $d->gr_akhir }}</td>
                            <td class="text-center">
                                <input type="checkbox" no_box="{{ $d->no_box }}" class="checkbox" name=""
                                    id="" value="{{ $d->id_cabut }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form action="{{ route('gudangsarang.save_formulir') }}" method="post">
                @csrf
                <x-theme.modal idModal="formulir" btnSave="Y" size="modal-lg" title="Formulir">
                    <div class="row">
                        <div class="col-lg-4 mb-6">
                            <label for="">Tanggal</label>
                            <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-lg-4">
                            <label for="">Pengawas</label>
                            <select name="id_pengawas" class="form-control" id="">
                                <option value="">Pilih Pengawas</option>
                                @foreach ($pengawas as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div id="load_formulir"></div>
                </x-theme.modal>
            </form>
        </section>
    </x-slot>
    @section('js')
        <script>
            $(document).ready(function() {
                $('#checkAll').change(function(e) {
                    e.preventDefault();
                    var checkedStatus = this.checked;

                    $('.checkbox').each(function() {
                        this.checked = checkedStatus;
                    });
                });

                $('.formulir').click(function(e) {
                    e.preventDefault();
                    var checkedBoxes = [];
                    $('.checkbox:checked').each(function() {
                        checkedBoxes.push($(this).val());
                    });

                    $.ajax({
                        type: "get",
                        url: "{{ route('gudangsarang.get_formulir') }}",
                        data: {
                            id_cabut: checkedBoxes
                        },
                        success: function(response) {
                            $('#load_formulir').html(response);
                        }
                    });

                });
            });
        </script>
    @endsection
</x-theme.app>
