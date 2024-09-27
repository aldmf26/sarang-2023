<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}
                {{ request()->get('tgl1') }} ~ {{ request()->get('tgl2') }}</h6>
        </div>
        {{-- <x-theme.button href="{{ route('absen.exportDetail', [$bulanGet,$tahunGet,$id_pengawas]) }}" icon="fa-file-excel" addClass="float-end"
        teks="Export" /> --}}
        <x-theme.button modal="Y" idModal="tambah" href="#" icon="fa-calendar-alt" addClass="float-end"
            teks="Detail" />

    </x-slot>

    <x-slot name="cardBody">
        <style>
            #sticky-header {
                position: sticky;
                top: 0;
                background-color: #fff;
                /* You can change the background color as needed */
                z-index: 100;
                /* Ensure the headers are above other elements */
            }

            .sticky-cell {
                position: sticky;
                left: 0;
                /* You can change the background color as needed */
                z-index: 99;
                /* Ensure the cells are above other elements */
            }
        </style>
        @csrf
        <section class="row">
            <div class="col-lg-12 mb-2">
                <table class="mb-2">
                    <tr>
                        <td>
                            <label for="">Pencarian : </label>
                        </td>
                        <td>
                            <input autofocus type="text" id="pencarian" class="form-control float-end">

                        </td>
                    </tr>
                </table>
                <table id="tblAldi" style="border:1px solid #435EBE;"
                    class="table table-bordered table-hover table-striped">
                    <thead style="border:1px solid black;">
                        <tr id="sticky-header">
                            <th class="dhead">Pgws</th>
                            <th class="dhead">Anak</th>
                            @foreach ($period as $date)
                                <th class="dhead">{{ $date->format('m/d') }}</th>
                            @endforeach
                            <th class="dhead">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($absen as $d)
                            <tr>
                                <td class="sticky-cell dhead text-white">{{ $d->name }}</td>
                                <td class="sticky-cell dhead text-white">{{ $d->nama }}</td>
                                @php
                                    $ttl = 0;
                                @endphp
                                @foreach ($period as $date)
                                    @php
                                        $hari = $date->format('d');
                                        $bulanH = $date->format('m');
                                        $tahunH = $date->format('Y');

                                        $getTgl = DB::table('absen')
                                            ->where([['id_anak', $d->id_anak], ['tgl', "$tahunH-$bulanH-$hari"],['bulan_dibayar', 9]])
                                            ->count();
                                        $ttl += $getTgl ?? 0;
                                    @endphp
                                    <td class="text-center"><span class="text-warning">{{ "$tahunH-$bulanH-$hari" }} == {{ $getTgl }}</span>{{ empty($getTgl) ? '-' : $getTgl }}</td>
                                @endforeach
                                <td class="text-center">{{ $ttl }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <form action="{{ route('absen.detailSum') }}" method="get">
            <x-theme.modal idModal="tambah" title="Detail Absen">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Dari</label>
                            <input type="date" name="tgl1" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Sampai</label>
                            <input type="date" name="tgl2" class="form-control">

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Pengawas</label>
                        <select name="id_pengawas" class="form-control select2" id="">
                            <option value="all">- ALL -</option>
                            @foreach ($pengawas as $d)
                                <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </x-theme.modal>
        </form>
    </x-slot>
    @section('scripts')
        <script>
            pencarian('pencarian', 'tblAldi')
        </script>
    @endsection

</x-theme.app>
