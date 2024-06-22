<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('pengiriman.update') }}" method="post">
            @csrf
            <div class="row">

                <hr>
                <x-theme.alert pesan="{{ session()->get('error') }}" />
                <div class="col-lg-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">Tgl</th>
                                <th class="dhead">No Grading</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                                <th class="dhead">Cek Qc</th>
                                <th class="dhead">No Barcode</th>
                            </tr>
                            @php
                                $id_pengiriman = explode(',', request()->get('id_pengiriman'));
                            @endphp
                            @foreach ($boxKirim as $i => $d)
                                <tr>
                                    <td>
                                        <input type="hidden" value="{{ $id_pengiriman[$i] }}" name="id_pengiriman[]">
                                        <input type="date" name="tgl_input[]" value="{{ $d->tgl_input }}"
                                            class="form-control">
                                    </td>
                                    <td><input type="text" name="no_grading[]" value="{{ $d->no_box }}"
                                            class="form-control"></td>
                                    <td><input type="text" name="pcs[]" value="{{ $d->pcs }}"
                                            class="form-control"></td>
                                    <td><input required type="text" name="gr[]" value="{{ $d->gr }}"
                                            class="form-control"></td>
                                    <td><input type="text" name="cek_qc[]" value="{{ $d->cek_qc }}"
                                            class="form-control"></td>
                                    <td><input type="text" name="no_barcode[]" value="{{ $d->no_barcode }}"
                                            class="form-control"></td>
                                </tr>
                            @endforeach
                        </thead>
                    </table>
                    <button class="btn btn-primary float-end" type="submit">Save</button>
                </div>
            </div>
        </form>
    </x-slot>
</x-theme.app>
