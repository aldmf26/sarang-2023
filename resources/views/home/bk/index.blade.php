<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="{{ route('bk.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" />
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
                        @php
                            $t = ['tipe', 'pgws', 'nama', 'tgl terima', 'pcs awal', 'pcs hcr', 'pcs flx', 'pcs ttl', 'ttl rp'];
                        @endphp
                        @foreach ($t as $d)
                            <th>{{ ucwords($d) }}</th>
                        @endforeach
                        <th>Aksi</th>
                    </tr>
                </thead>
                
            </table>
        </section>

    </x-slot>
</x-theme.app>