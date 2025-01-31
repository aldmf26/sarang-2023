<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                class="fas fa-plus"></i>Data</a>
        <a href="{{ route('hrga3_1.print', ['divisi' => $id_divisi]) }}" target="_blank"
            class="btn btn-primary float-end me-2"><i class="fas fa-print"></i>
            Print</a>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th class="dhead text-center" rowspan="2">No</th>
                            <th class="dhead text-center" rowspan="2">Tanggal Informasi</th>
                            <th class="dhead text-center" rowspan="2">Jenis Pelatihan</th>
                            <th class="dhead text-center" rowspan="2">Sasaran Pelatihan</th>
                            <th class="dhead text-center" rowspan="2">Tema Pelatihan <br> [yang ditawarkan]</th>
                            <th class="dhead text-center" rowspan="2">Sumber Informasi</th>
                            <th class="dhead text-center" rowspan="2">Personil Penghubung</th>
                            <th class="dhead text-center">No.Telp</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">e-Mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tawaran as $t)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ tanggal($t->tgl_informasi) }}</td>
                                <td>{{ $t->jenis_pelatihan }}</td>
                                <td>{{ $t->sasaran_pelatihan }}</td>
                                <td>{{ $t->tema_pelatihan }}</td>
                                <td>{{ $t->sumber_informasi }}</td>
                                <td>{{ $t->personil_penghubung }}</td>
                                <td>{{ $t->no_telp }} <br> {{ $t->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <form action="{{ route('hrga3_1.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="Informasi Tawaran Pelatihan" size="modal-xl" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="">Tanggal Informasi</label>
                            <input type="date" name="tanggal_informasi" class="form-control" required>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Jenis Pelatihan</label>
                            <input type="text" name="jenis_pelatihan" class="form-control" required>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Sasaran Pelatihan</label>
                            <input type="text" name="sasaran_pelatihan" class="form-control" required>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Tema Pelatihan</label>
                            <input type="text" name="tema_pelatihan" class="form-control" required>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Sumber Informasi</label>
                            <input type="text" name="sumber_informasi" class="form-control" required>
                        </div>
                        <div class="col-lg-3 mt-2">
                            <label for="">Personil Penghubung</label>
                            <input type="text" name="personil_penghubung" class="form-control" required>
                        </div>
                        <div class="col-lg-3 mt-2">
                            <label for="">No Telp</label>
                            <input type="text" name="no_telp" class="form-control" required>
                        </div>
                        <div class="col-lg-3 mt-2">
                            <label for="">Email</label>
                            <input type="text" name="email" class="form-control" required>
                        </div>

                    </div>
                </x-theme.modal>
            </form>

        </section>
        @section('scripts')
        @endsection
    </x-slot>

</x-theme.app>
