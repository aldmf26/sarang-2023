<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
            <a href="" class="btn btn-primary float-end " data-bs-toggle="modal" data-bs-target="#print"><i
                    class="fas fa-print"></i> Print</a>
            <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                    class="fas fa-plus"></i>Data</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">

            <div class="col-lg-12">

                <table class="table table-bordered" id="table">
                    <thead>
                        <th class="dhead">#</th>
                        <th class="dhead">Divisi</th>
                        <th class="dhead">Bulan</th>
                        <th class="dhead">Tahun</th>
                        <th class="dhead">Tanggal realisasi</th>
                        <th class="dhead">Aksi</th>
                    </thead>
                    <tbody>
                        @foreach ($jadwal_gap as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->divisi }}</td>
                                <td>{{ $d->bulan }}</td>
                                <td>{{ $d->tahun }}</td>
                                <td>{{ $d->tgl_awal_realisasi }} ~ {{ $d->tgl_akhir_realisasi }}</td>
                                <td>
                                    <a href="" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

            <form action="{{ route('hrga2_5.save_jadwal') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="Jadwal Gap" size="modal-lg-max" btnSave="Y">
                    <div class="row">

                        <div class="col-lg-3">
                            <label for="">Divisi</label>
                            <select name="id_divisi" id="" class="form-control">
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisis as $d)
                                    <option value="{{ $d->id }}">{{ $d->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Bulan</label>
                            <select name="bulan" id="" class="form-control">
                                <option value="">Pilih Bulan</option>
                                @foreach ($bulan as $b)
                                    <option value="{{ $b->bulan }}">{{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Tahun</label>
                            <select name="tahun" id="" class="form-control">
                                <option value="">Pilih Tahun</option>
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y', strtotime('+1 year')) }}">
                                    {{ date('Y', strtotime('+1 year')) }}</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Tanggal realisasi dari</label>
                            <input type="date" name="tgl_awal_realisasi" id="" class="form-control">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Tanggal realisasi sampai</label>
                            <input type="date" name="tgl_akhir_realisasi" id="" class="form-control">
                        </div>

                    </div>
                </x-theme.modal>
            </form>

            <form action="{{ route('hrga2_5.print') }}" method="get" target="_blank">
                <x-theme.modal idModal="print" title="Print Jadwal Gap" size="modal-sm" btnSave="Y">
                    <div class="row">

                        <div class="col-lg-12">
                            <label for="">Tahun</label>
                            <select name="tahun" id="" class="form-control">
                                <option value="">Pilih Tahun</option>
                                @foreach ($tahun as $d)
                                    <option value="{{ $d->tahun }}">{{ $d->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </x-theme.modal>
            </form>

        </section>



        @section('scripts')
        @endsection
    </x-slot>

</x-theme.app>
