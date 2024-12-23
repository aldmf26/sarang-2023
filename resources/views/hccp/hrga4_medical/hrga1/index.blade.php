<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                class="fas fa-plus"></i>Data</a>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#print"><i
                class="fas fa-print"></i> Print</a>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwal as $j)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $j->nama }}</td>
                                <td>{{ $j->divisi }}</td>
                                <td>{{ $j->bulan }}</td>
                                <td>{{ $j->tahun }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <form action="{{ route('hrga4_1.print') }}" method="get" target="_blank">
                <x-theme.modal idModal="print" title="Print Jadwal Medical Check Up" size="modal-m" btnSave="Y">
                    <div class="row">

                        <div class="col-lg-12">
                            <label for="">Tahun</label>
                            @if (empty($tahun))
                                <p>Belum ada data</p>
                            @else
                                <select name="tahun" id="" class="form-control">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $d)
                                        <option value="{{ $d->tahun }}">{{ $d->tahun }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <input type="hidden" value="{{ $divisi }}" name="divisi">

                        </div>
                    </div>
                </x-theme.modal>
            </form>

            <form action="{{ route('hrga4_1.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="Jadwal Meidcal Check up" size="modal-m" btnSave="Y">
                    <div class="row">

                        <div class="col-lg-4">
                            <label for="">Karyawan</label>
                            <select name="id_karyawan" id="" class="form-control">
                                <option value="">Pilih Karyawan</option>
                                @foreach ($karyawan as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="">Bulan</label>
                            <select name="bulan" id="" class="form-control">
                                <option value="">Pilih Bulan</option>
                                @foreach ($bulan as $b)
                                    <option value="{{ $b->bulan }}">{{ $b->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="">Tahun</label>
                            <select name="tahun" id="" class="form-control">
                                <option value="">Pilih Tahun</option>
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y', strtotime('+1 year')) }}">
                                    {{ date('Y', strtotime('+1 year')) }}</option>
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
