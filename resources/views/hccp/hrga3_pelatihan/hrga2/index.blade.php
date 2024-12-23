<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                class="fas fa-plus"></i>Data</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#print" class="btn btn-primary float-end me-2"><i
                class="fas fa-print"></i>
            Print</a>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th class="dhead text-center">No</th>
                            <th class="dhead text-center">Materi Pelatihan</th>
                            <th class="dhead text-center">I/E <br> *</th>
                            <th class="dhead text-center">Narasumber</th>
                            <th class="dhead text-center">Sasaran Peserta</th>
                            <th class="dhead text-center">Tanggal rencana</th>
                            <th class="dhead text-center">Tanggal Realisasi</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($program as $t)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $t->materi_pelatihan }}</td>
                                <td>{{ $t->i }}</td>
                                <td>{{ $t->narasumber }}</td>
                                <td>{{ $t->sasaran_peserta }}</td>
                                <td>{{ $t->tgl_rencana }}</td>
                                <td>{{ $t->tgl_realisasi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <form action="{{ route('hrga3_2.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="Program Pelatihan Tahunan" size="modal-xl" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="">Materi Pelatihan</label>
                            <input type="text" name="materi_pelatihan" class="form-control" required>
                        </div>
                        <div class="col-lg-3">
                            <label for="">I/E</label>
                            <select name="i" class="form-control" id="">
                                <option value="I">I</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Narasumber</label>
                            <input type="text" name="narasumber" class="form-control" required>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Sasaran Peserta</label>
                            <input type="text" name="sasaran_peserta" class="form-control" required>
                        </div>
                        <div class="col-lg-3 mt-2">
                            <label for="">Tanggal Rencana</label>
                            <input type="date" name="tgl_rencana" class="form-control" required>
                        </div>
                        <div class="col-lg-3 mt-2">
                            <label for="">Tanggal Realisasi</label>
                            <input type="date" name="tgl_realisasi" class="form-control" required>
                        </div>


                    </div>
                </x-theme.modal>
            </form>
            <form action="{{ route('hrga3_2.print') }}" method="get" target="_blank">
                <x-theme.modal idModal="print" title="Print Program Pelatihan Tahunan" size="modal-sm" btnSave="Y">
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
