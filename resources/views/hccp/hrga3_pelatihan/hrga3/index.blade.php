<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                class="fas fa-plus"></i>Data</a>
        <a href="{{ route('hrga3_3.print', ['divisi' => $divisi]) }}" target="_blank"
            class="btn btn-primary float-end me-2"><i class="fas fa-print"></i>
            Print</a>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered " id="table">
                    <thead>
                        <tr>
                            <th class="text-center dhead">#</th>
                            <th class="text-center dhead">Nama Calon Peserta yang Diusulkan</th>
                            <th class="text-center dhead">NIP</th>
                            <th class="text-center dhead">Pengusul</th>
                            <th class="text-center dhead">Usulan Jenis Pelatihan <br>
                                [yang sesuai dengan peningkatan kompetensi]</th>
                            <th class="text-center dhead">Usulan Waktu Pelaksanaan</th>
                            <th class="text-center dhead">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usulan as $u)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $u->nama }}</td>
                                <td>{{ $u->id_karyawan }}</td>
                                <td>{{ $u->pengusul }}</td>
                                <td>{{ $u->usulan }}</td>
                                <td>{{ tanggal($u->waktu) }}</td>
                                <td>{{ $u->alasan }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <form action="{{ route('hrga3_3.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="{{ $title }}" size="modal-xl" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="">Pengusul</label>
                            <input type="text" name="pengusul" class="form-control" required>
                        </div>
                        <div class="col-lg-4">
                            <label for="">Usulan Jenis Pelatihan</label>
                            <input type="text" name="usulan" class="form-control" required>
                        </div>
                        <div class="col-lg-4">
                            <label for="">Usulan Waktu Pelatihan</label>
                            <input type="date" name="waktu" class="form-control" required>
                            <input type="hidden" name="id_divisi" class="form-control" value="{{ $divisi }}">
                        </div>
                        <div class="col-lg-4 mt-2">
                            <label for="">Alasan</label>
                            <input type="text" name="alasan" class="form-control" required>
                        </div>
                        <div class="col-lg-6 mt-2">
                            <label for="">Nama Calon peserta</label>
                            <select class="choices form-select multiple-remove" multiple="multiple"
                                name="id_karyawan[]">
                                <optgroup label="Figures">
                                    @foreach ($hasil_wawancara as $h)
                                        <option value="{{ $h->id }}">{{ $h->nama }}</option>
                                    @endforeach
                                </optgroup>

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
