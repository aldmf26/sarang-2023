<x-theme.app title="{{ $title }} " table="Y" sizeCard="8" cont="container-fluid">
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
            <div class="col-lg-4">
                @include('home.gudang_sarang.nav')
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered" id="nanda">
                    <thead>
                        <tr>
                            <th width="5">#</th>
                            <th>tanggal</th>
                            <th>no box</th>
                            <th>tipe</th>
                            <th>ket</th>
                            <th>warna</th>
                            <th class="text-end">Pcs Bk</th>
                            <th class="text-end">Gr Bk</th>
                            <th class="text-end">Pcs Cbt</th>
                            <th class="text-end">Gr Cbt</th>
                            <th class="text-end">Pcs Sisa</th>
                            <th class="text-end">Gr Sisa</th>
                            <th class="text-center">
                                <input type="checkbox" name="" id="checkAll">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabut as $no => $d)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ date('d-m-Y', strtotime($d->tgl)) }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->tipe }}</td>
                                <td>{{ $d->ket }}</td>
                                <td>{{ $d->warna }}</td>
                                <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($d->pcs_cabut, 0) }}</td>
                                <td class="text-end">{{ number_format($d->gr_cabut, 0) }}</td>
                                <td class="text-end">{{ number_format($d->pcs_awal - $d->pcs_cabut, 0) }}</td>
                                <td class="text-end">{{ number_format($d->gr_awal - $d->gr_cabut, 0) }}</td>
                                <td>
                                    <input type="checkbox" no_box="{{ $d->no_box }}" class="checkbox" name=""
                                        id="" value="{{ $d->no_box }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form action="{{ route('gudangsarang.save_formulir_cabut') }}" method="post">
                @csrf
                <x-theme.modal idModal="formulir" btnSave="Y" size="modal-lg" title="Formulir">
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
                        url: "{{ route('gudangsarang.get_formulircabut') }}",
                        data: {
                            no_box: checkedBoxes
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
