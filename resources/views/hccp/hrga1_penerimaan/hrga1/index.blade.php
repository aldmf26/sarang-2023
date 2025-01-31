<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <a class="btn btn-sm btn-primary" href="{{route('hrga1.create')}}"><i class="fa fa-plus"></i> Tambah Permohonan</a>
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
            <tbody>
                @foreach ($hrga1 as $d)
                    <tr>
                        <td>{{ tanggal($d->tgl_dibutuhkan) }}</td>
                        <td>{{ $d->status_posisi }}</td>
                        <td>{{ $d->jabatan }}</td>
                        <td>{{ $d->jumlah }} Orang</td>
                        <td>{{ $d->alasan_penambahan }}</td>
                        <td>{{ $d->diajukan_oleh }}</td>
                        <td>{{ $d->admin }}</td>
                        <td class="d-flex gap-1">
                            <a target="_blank" href="{{ route('hrga1.export', $d->id) }}" class="btn btn-sm btn-primary"><i
                                    class="fa fa-print"></i></a>
                            <a href="{{ route('hrga1.edit', $d->id) }}" class="btn btn-sm btn-primary"><i
                                    class="fa fa-edit"></i></a>
                            <form action="{{ route('hrga1.delete', $d->id) }}" method="post" 
                                onsubmit="return confirm('Anda yakin ingin menghapus data ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger"><i
                                    class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </x-slot>

</x-theme.app>
