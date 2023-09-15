<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">
    <div class="row justify-content-center">
        <ul class="nav nav-pills float-start">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Aktiva</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Peralatan</a>

            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Atk</a>
            </li>
        </ul>
        <div class="col-lg-9">
            <table class="table small table-bordered table-striped table-hover" id="table">
                <thead>
                    <tr>
                        <th class="dhead text-end">No Box</th>
                        <th class="dhead text-end">Pcs Awal Bk</th>
                        <th class="dhead text-end">Gr Awal Bk</th>
                        <th class="dhead">Bulan</th>
                        <th class="dhead">Pengawas</th>
                        <th class="dhead text-end">Pcs Awal Kerja</th>
                        <th class="dhead text-end">Gr Awal Kerja</th>
                        <th class="dhead text-end">Total Rupiah</th>
                        <th class="dhead text-end">Pcs Sisa Bk</th>
                        <th class="dhead text-end">Gr Sisa Bk</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($datas as $d)
                    <tr>
                        <td align="right">
                            <a class="detail" target="_blank" href="{{ route('dashboard.detail', $d->no_box) }}">{{
                                number_format($d->no_box, 0) }} <i class="me-2 fas fa-eye"></i></a>
                        </td>
                        <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                        <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                        <td>{{ date('M Y', strtotime($d->tgl)) }}</td>
                        <td>{{ strtoupper($d->name) }}
                        </td>
                        <td align="right">{{ number_format($d->pcs_cabut, 0) }}</td>
                        <td align="right">{{ number_format($d->gr_cabut, 0) }}</td>
                        <td align="right">Rp. {{number_format($d->rupiah,0)}}</td>
                        <td align="right">{{ number_format($d->pcs_awal - $d->pcs_cabut , 0) }}</td>
                        <td align="right">{{ number_format($d->gr_awal - $d->gr_cabut , 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- <x-theme.modal title="Detail Gaji" scroll="Y" size="modal-max" idModal="detail">
        <div id="get_detail">
        </div>
    </x-theme.modal> --}}

    @section('scripts')
    <script>
        // detail('detail', 'nobox', 'dashboard/detail', 'get_detail')
    </script>
    @endsection
</x-theme.app>