<x-theme.app title="Log" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6>Log</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <ul class="bg-info nav nav-pills float-start mt-4">
                    @foreach ($pengawas as $d)
                        <li class="nav-item">
                            <a class="nav-link text-white {{ $d->id_pengawas == $id_pengawas ? 'active' : '' }}"
                                aria-current="page"
                                href="{{ route('log', [
                                    'id_pengawas' => $d->id_pengawas,
                                ]) }}">{{ $d->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-12">
                <table class="table" id="table1">
                    <thead>
                        <tr>
                            <th class="dhead" width="5">#</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead">Aktivitas</th>
                            <th class="dhead">Jam</th>
                            {{-- <th width="20%">Aksi</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($log as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->description }}</td>
                                <td>{{ \Carbon\Carbon::parse($d->created_at)->locale('id')->isoFormat('D MMMM Y, hh:mm a') }}</td>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </x-slot>
</x-theme.app>