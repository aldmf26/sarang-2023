<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
            <a href="{{ route('datapegawai.export') }}" class="btn btn-primary float-end"><i class="fas fa-file-excel"></i>
                Export</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Divisi / Dept</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin/ <br>Tanggal lahir</th>
                            <th>Status</th>
                            <th>Tanggal Masuk</th>
                            <th>Posisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Cabut bulu</td>
                            <td>Jennah</td>
                            <td>L / 01 Februari 2006</td>
                            <td>Tetap</td>
                            <td>01 Februari 2023</td>
                            <td>pengawas</td>
                        </tr>


                    </tbody>

                </table>
            </div>


        </section>



        @section('scripts')
        @endsection
    </x-slot>

</x-theme.app>
