<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <a onclick="return window.print();" data-mdb-ripple-init class="float-end btn btn-light text-capitalize border-0"
            data-mdb-ripple-color="dark"><i class="fas fa-home text-primary"></i> Kembali</a>
        <h5 class="float-start mt-1">{{ $title }}</h5>

    </x-slot>

    <x-slot name="cardBody">
        <style>
            @media print {
                .print_hilang {
                    display: none;
                }
                .print_ada {
                    display: block;
                }

                .section {
                    page-break-after: always;
                }
            }
        </style>
        <form action="{{ route('pengawas.save_invoice') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="container mb-5 mt-3">
                        <div class="row d-flex align-items-baseline">
                            <div class="col-xl-9">
                                <p style="color: #7e8d9f;font-size: 20px;"><strong>Invoice Karyawan Cabut</strong>
                                </p>
                            </div>
                            <div class="col-xl-3 float-end">
                            </div>
                            <hr>
                        </div>

                        <div class="container">
                            @if ($cekSudahSave->count())
                                
                            <div class="d-flex justify-content-between">
                                <p style="color: #7e8d9f;font-size: 18px;"><strong>No Invoice:
                                        #{{ $no_invoice }}</strong>
                                </p>
                                <p style="color: #7e8d9f;font-size: 15px;"><strong>Tgl Lunas:
                                        {{ tanggal($cekSudahSave[0]->tgl_lunas) }}</strong>
                            </div>
                            <p class="" style="color: #7e8d9f;font-size: 15px;"><strong>Pembayar:
                                    {{ $cekSudahSave[0]->pembayar }}</strong>
                            </p>
                            @endif

                            <div class="row print_hilang {{request()->get('no_invoice') ? 'd-none' : ''}}">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="">Tgl Lunas</label>
                                            <input required type="date" value="{{ date('Y-m-d') }}" name="tgl_lunas"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="">Pembayar</label>
                                            <input required type="text" value="{{ auth()->user()->name }}"
                                                name="pembayar" class="form-control">
                                            <input required type="hidden" value="{{ $no_invoice }}" name="no_invoice"
                                                class="form-control">

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row my-2 mx-1 justify-content-center">
                                <table class="table table-striped table-borderless">
                                    <thead style="background-color:#84B0CA ;" class="text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Tgl Masuk</th>
                                            <th>Tgl Dibayar</th>
                                            <th>Nama Karyawan</th>
                                            <th>Pembawa</th>
                                            <th>Pengawas</th>
                                            <th>Keterangan</th>
                                            <th>Periode</th>
                                            <th class="text-end">Komisi Rp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($anak as $no => $d)
                                            <tr>
                                                <td>
                                                    {{ $no + 1 }}
                                                    <input type="hidden" name="id_anak[]" value="{{ $d->id_anak }}">
                                                </td>
                                                <td>{{ tanggal($d->tgl_masuk) }}</td>
                                                <td>{{ !$d->tgl_dibayar ? '' : tanggal($d->tgl_dibayar) }}</td>
                                                <td>{{ ucwords($d->nama) }}</td>
                                                <td>{{ ucwords($d->pembawa) }}</td>
                                                <td>{{ ucwords($d->name) }}</td>
                                                <td>{{ 'dibayar ' . number_format($d->komisi, 0) }}</td>
                                                <td>{{ "$d->periode Bulan" }}</td>
                                                <td align="right">{{ number_format($d->komisi, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xl-8">

                                </div>
                                <div class="col-lg-12">

                                    <p class="text-black float-end"><span class="text-black me-3"> Grand
                                            Total</span><span
                                            style="font-size: 25px;">{{ number_format(sumCol($anak, 'komisi'), 0) }}</span>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row float-end">
                                <div class="col-xl-12">
                                    <a onclick="return window.print();" data-mdb-ripple-init
                                        class="btn btn-light text-capitalize border-0" data-mdb-ripple-color="dark"><i
                                            class="fas fa-print text-primary"></i> Print</a>
                                    @if (!$cekSudahSave)
                                        <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                            class="btn btn-primary text-capitalize"
                                            style="background-color:#60bdf3 ;">Simpan</button>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </x-slot>


</x-theme.app>
