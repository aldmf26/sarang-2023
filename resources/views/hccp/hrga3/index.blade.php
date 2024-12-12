<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <a class="btn btn-sm btn-primary" href="{{route('hrga3.create')}}"><i class="fa fa-plus"></i> Tambah Hasil Evaluasi Karyawan</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <table class="table" id="table1">
            <thead>
                <tr>
                    <th class="dhead">Tgl Dibutuhkan</th>
                    <th class="dhead">Status</th>
                    <th class="dhead">Jabatan</th>
                    <th class="dhead">Jumlah</th>
                    <th class="dhead">Alasan Penambahan</th>
                    <th class="dhead">Diajukan Oleh</th>
                    <th class="dhead">Admin</th>
                    <th class="dhead">Aksi</th>
                </tr>
            </thead>
        
        </table>

    </x-slot>

</x-theme.app>
