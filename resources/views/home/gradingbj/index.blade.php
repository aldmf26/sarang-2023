<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }} <span x-text="tes"></span></h6>
            <div>
                {{-- <a href="{{ route('gradingbj.gudang_siap_kirim') }}" class="btn btn-sm btn-primary">asd</a> --}}
                <x-theme.button href="{{ route('gradingbj.gudang_siap_kirim') }}" icon="fa-warehouse"
                    teks="Gudang Siap Kirim" />
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row" x-data="{ cek: [] }">
            <div class="col-lg-12">
            </div>
            <div class="col-lg-12 mt-2">
                <div class="row">
                    <div class="col-lg-9">
                        <input type="text" id="tbl1input" class="form-control form-control-sm mb-2"
                            placeholder="cari">
                    </div>
                    <div class="col-lg-3">
                        <form action="{{ route('gradingbj.grading') }}" method="post">
                            @csrf
                            <input type="hidden" name="no_box" class="form-control" :value="cek">
                            <button x-transition x-show="cek.length" class="btn-block btn btn-sm btn-primary float-end"
                                type="submit">
                                <i class="fas fa-plus"></i>
                                Grading
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button>
                        </form>
                    </div>
                </div>
                <div style="overflow-y: scroll; height: 500px">
                    <table id="tbl1" class="table table-hover table-striped">
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
        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
