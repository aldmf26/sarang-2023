<x-theme.app title="{{ $title }}" table="Y" sizeCard="7">
    <x-slot name="cardHeader">
        <h6 class="float-start">{{ $title }} <span style="font-size: 13px">{{ tanggal($tgl1) }} ~
                {{ tanggal($tgl2) }}</span></h6>
        <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Absen Stgh Hari" />
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Nama</th>
                        <th class="text-end">Kelas</th>
                        <th class="text-end">Ttl</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absen as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ ucwords($d->nama) }}</td>
                            <td align="right">{{ $d->kelas }}</td>
                            @php
                                $count = (1 / $d->count) * $d->count - $d->countStgh;
                            @endphp
                            <td align="right">
                                {{ number_format($d->count > 1 ? $d->ttlBub : $d->ttl_absen + $d->countStgh, 1) }}</td>
                            <td align="center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                                    class="btn btn-sm btn-{{ $d->count > 1 ? 'danger' : 'primary' }} detail"
                                    id_anak="{{ $d->id_anak }}"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <form action="{{ route('absen.create_stgh_hari') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="Tambah Absen Setengah Hari">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">Nama</th>
                                <th class="dhead">Tanggal</th>
                                <th class="dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="id_anak[]" class="select2" id="">
                                        <option value="">Pilih Anak</option>
                                        @foreach ($anak as $s)
                                            <option value="{{ $s->id_anak }}">{{ strtoupper($s->nama) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="date" value="{{ date('Y-m-d') }}" name="tgl[]"
                                        class="form-control">
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tbody id="tbh_baris">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">
                                    <button type="button" class="btn btn-block btn-lg tbh_baris"
                                        style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                                        <i class="fas fa-plus"></i> Tambah Baris Baru
                                    </button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </x-theme.modal>
            </form>
            <x-theme.modal idModal="detail" title="Detail Absen">
                <div id="load_detail"></div>
            </x-theme.modal>

        </section>
        @section('scripts')
            <script>
                detail('detail', 'id_anak', 'absen/detail', 'load_detail')
                plusRow(1, 'tbh_baris', "absen/tbh_baris")
            </script>
        @endsection
    </x-slot>
</x-theme.app>
