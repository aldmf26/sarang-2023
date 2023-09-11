<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button idModal="tambah" modal="Y" href="#" icon="fa-plus" addClass="float-end" teks="Tambah" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Lot</th>
                        <th>No Box</th>
                        <th>Tipe</th>
                        <th>Ket</th>
                        <th>Warna</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                
            </table>
        </section>
        <form action="{{ route('sortir.selesai_cabut') }}" method="get">
            @csrf
            <x-theme.modal idModal="tambah" title="Tambah Stok" btnSave="Y">
                <div class="row">
                    
                </div>
            </x-theme.modal>
        </form>
    </x-slot>
</x-theme.app>