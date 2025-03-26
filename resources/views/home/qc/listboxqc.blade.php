<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('qc.save_akhir') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No Box Grading</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Gr Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qc as $q)
                                <tr>
                                    <td>{{ $q->box_pengiriman }}</td>
                                    <td>{{ $q->grade }}</td>
                                    <td class="text-end">{{ $q->pcs_awal }}</td>
                                    <td class="text-end">{{ $q->gr_awal }}</td>
                                    <td width="20%">
                                        <input type="text" name="gr_akhir[]" class="form-control text-end"
                                            value="{{ $q->gr_akhir == 0 ? $q->gr_awal : $q->gr_akhir }}">
                                        <input type="hidden" name="box_pengiriman[]" value="{{ $q->box_pengiriman }}">
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12">
                    <hr>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Simpan</button>
                    <a href="{{ route('qc.listqc') }}" class="btn  btn-warning me-2 float-end">Kembali</a>
                </div>

            </div>

        </form>


    </x-slot>

</x-theme.app>
