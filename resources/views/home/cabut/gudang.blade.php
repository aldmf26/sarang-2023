<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <h6>{{ $title }}</h6>
        <div class="row">
            <div class="col-lg-4">
                <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                <table id="tbl1" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="3">Box belum diambil</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bk as $d)
                            <tr>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs }}</td>
                                <td align="right">{{ $d->gr }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl2input" class="form-control form-control-sm mb-2" placeholder="cari">

                <table id="tbl2" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="3">Box diambil</th>
                        </tr>
                        <tr>

                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabut as $d)
                            <tr>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs }}</td>
                                <td align="right">{{ $d->gr }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">

                <table id="tbl3" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="3">Box selesai blm diambil ctk</th>
                        </tr>
                        <tr>

                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabutSelesai as $d)
                            <tr>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs }}</td>
                                <td align="right">{{ $d->gr }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>



        </div>
        @section('scripts')
            <script>
                pencarian('tbl1input','tbl1')
                pencarian('tbl2input','tbl2')
                pencarian('tbl3input','tbl3')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
