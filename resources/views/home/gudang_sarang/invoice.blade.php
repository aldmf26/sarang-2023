<x-theme.app title="{{ $title }} " table="Y" sizeCard="9">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <x-theme.btn_filter />
            </div>
        </div>

        <ul class="nav nav-pills float-start">
            <li class="nav-item">
                <a class="nav-link {{ $route == $routeSekarang && $kategori == 'cetak' ? 'active' : '' }}"
                    aria-current="page" href="{{ route($routeSekarang) }}">Cetak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $route == $routeSekarang && $kategori == 'sortir' ? 'active' : '' }}"
                    aria-current="page" href="{{ route($routeSekarang, ['kategori' => 'sortir']) }}">Sortir</a>
            </li>


        </ul>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="nanda">
                <thead>
                    <tr>
                        <th class="dhead" width="5">#</th>
                        <th class="dhead">Tanggal</th>
                        <th class="dhead">No Invoice</th>
                        <th class="dhead">Nama Pemberi</th>
                        <th class="dhead">Nama Penerima</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulir as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td>
                                {{ $d->no_invoice }}
                            </td>
                            <td>{{ $d->pemberi }}</td>
                            <td>{{ $d->penerima }}</td>
                            <td class="text-end">{{ $d->pcs }}</td>
                            <td class="text-end">{{ $d->gr }}</td>
                            <td>
                                @php
                                    $param = ['kategori' => 'cetak', 'no_invoice' => $d->no_invoice];
                                    $getCtk = DB::table('formulir_sarang as a')
                                        ->select('a.no_box')
                                        ->join('cetak_new as b', 'a.no_box', 'b.no_box')
                                        ->where('a.no_invoice', $d->no_invoice)
                                        ->first();
                                @endphp
                                @if (!$getCtk)
                                    <a onclick="return confirm('Yakin dihapus ?')"
                                        href="{{ route('gudangsarang.batal', $param) }}">
                                        <span class="badge bg-danger">Batal</span>
                                    </a>

                                    <a href="#" class="edit" data-no_invoice="{{ $d->no_invoice }}"
                                        data-kategori="cetak">
                                        <span class="badge bg-primary">Edit</span>
                                    </a>

                                    <a onclick="return confirm('Yakin diselesaikan ?')"
                                        href="{{ route('gudangsarang.selesai', $param) }}">
                                        <span class="badge bg-success">Selesai</span>
                                    </a>
                                @else
                                    <a href="{{ $kategori == 'cetak' ? route('gudangsarang.print_formulir', ['no_invoice' => $d->no_invoice]) : "/home/cetaknew/formulir/$d->no_invoice" }}"
                                        target="_blank">
                                        <span class="badge bg-primary">Print</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </section>

        <form action="{{ route('gudangsarang.update_invoice') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Po" idModal="edit" size="modal-lg">
                <div class="loading d-none">
                    <x-theme.loading />
                </div>
                <div id="load_edit"></div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(document).on('click', '.edit', function(e) {
                    e.preventDefault();

                    var no_invoice = $(this).data('no_invoice');
                    var kategori = $(this).data('kategori');

                    $("#edit").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gudangsarang.load_edit_invoice') }}",
                        data: {
                            no_invoice,
                            kategori,
                        },
                        beforeSend: function() {
                            $("#load_edit").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#load_edit").html(r);
                            pencarian('inputTbl', 'tbl1')
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>