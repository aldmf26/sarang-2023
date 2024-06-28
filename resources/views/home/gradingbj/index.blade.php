<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">

        <section class="row" x-data="{ cek: [] }">
            <div class="col-lg-12">
            </div>
            <div class="col-lg-12 mt-2">
                <div class="row">
                    <div class="col-lg-5">
                        <input type="text" id="tbl1input" class="form-control form-control-sm mb-2"
                            placeholder="cari">
                    </div>

                    <div class="col-lg-7">
                        <form action="{{ route('gradingbj.grading') }}" method="post">
                            @csrf
                            <a href="#" class="btn btn-sm btn-info"
                                href=""><i class="fa fa-warehouse"></i> Gudang</a>
                            <a data-bs-toggle="modal" data-bs-target="#import" class="btn btn-sm btn-primary"
                                href="">Import</a>
                            <button type="submit" name="submit" value="export" class="btn btn-sm btn-primary"
                                href="" x-show="cek.length">Export</button>
                            <input type="hidden" name="no_box" class="form-control" :value="cek">
                            <button name="submit" value="grading" x-transition x-show="cek.length"
                                class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-plus"></i>
                                Grading
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button>
                            <button name="submit" value="selisih" x-transition x-show="cek.length"
                                class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-plus"></i>
                                Selisih
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button>
                        </form>
                    </div>
                </div>
                <div style="overflow-y: scroll; height: 500px">
                    <x-theme.alert pesan="{{ session()->get('error') }}" />
                    <table id="tbl1" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                <th class="dhead">Tanggal</th>
                                {{-- <th class="dhead">No Invoice</th> --}}
                                <th class="dhead">No Box</th>
                                <th class="dhead">Pemberi</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formulir as $i => $d)
                                <tr class="pointer"
                                    @click="cek.includes('{{ $d->no_box }}') ? cek = cek.filter(x => x !== '{{ $d->no_box }}') : cek.push('{{ $d->no_box }}')">
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ tanggal($d->tanggal) }}</td>
                                    {{-- <td>{{ $d->no_invoice }}</td> --}}
                                    <td>{{ $d->no_box }}</td>
                                    <td>{{ $d->pemberi }}</td>
                                    <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                                    <td align="center">
                                        <input type="checkbox" class="form-check"
                                            :checked="cek.includes('{{ $d->no_box }}')" name="id[]"
                                            id="" value="{{ $d->no_box }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <form action="{{ route('gradingbj.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Import" idModal="import">
                <input type="file" name="file" class="form-control">
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
