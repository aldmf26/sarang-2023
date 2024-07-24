<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        @include('home.cabut.view_bulandibayar')
    </x-slot>

    <x-slot name="cardBody">
        
        {{-- <form action="{{ route('penutup.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
                <div class="col">
                    <label for="file" class="form-label">Pilih File</label>
                    <input class="form-control" type="file" id="file" name="file">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit">Import</button>
                </div>
            </div>
        </form> --}}
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Bulan</th>
                        <th>Pcs Akhir</th>
                        <th>Gr Akhir</th>
                        <th>Ttl Rp</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                
            </table>
        </section>
    </x-slot>
</x-theme.app>