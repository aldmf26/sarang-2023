<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('gradingbj.createUlang') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas</th>
                                <th class="dhead" width="150">No Nota</th>
                                <th class="dhead">Tgl</th>
                            </tr>

                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $admin }}</td>
                                <td>{{ $no_invoice }}</td>
                                <td>{{ tanggal($tgl) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h6>Box Dipilih <span class="text-success">Partai : {{ $nm_partai }} </span></h6>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Tipe</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                <th class="dhead text-end">Total Rp</th>
                            </tr>
                        </thead>
                        <thead class="bg-white">
                            <tr>
                                <th>Total</th>
                                <th></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($box_grading as $b)
                                <tr>
                                    <td>{{ $b->no_box_sortir }}</td>
                                    <td class="text-center">{{ $b->tipe }}</td>
                                    <td class="text-end">{{ $b->pcs }}</td>
                                    <td class="text-end">{{ $b->gr }}</td>
                                    <td class="text-end"></td>
                                    @php
                                        $total =
                                            $b->cost_bk + $b->cost_cbt + $b->cost_ctk + $b->cost_eo + $b->cost_sortir;
                                    @endphp
                                    <td class="text-end">{{ number_format($total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <h6>Hasil Grading</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end" width="200">Pcs</th>
                                <th class="dhead text-end" width="200">Gr</th>
                                <th class="dhead " width="200">Box Sp</th>
                                <th class="dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <h6>Total</h6>
                                </td>
                                <td class="text-end">

                                </td>
                                <td class="text-end">

                                </td>
                            </tr>



                        </tbody>
                    </table>
                </div>
            </div>


        </form>
        @section('scripts')
            <script>
                clickSelectInput('form-control')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
