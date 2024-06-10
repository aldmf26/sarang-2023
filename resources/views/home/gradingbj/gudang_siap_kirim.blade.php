<x-theme.app title="{{ $title }}" table="Y" sizeCard="7">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>
                {{-- <x-theme.button href="{{ route('gradingbj.gudang_siap_kirim') }}" icon="fa-warehouse" teks="Gudang Siap Kirim" /> --}}
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <section>
            <table id="tbl1" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="dhead">No Box Grading</th>
                        <th class="dhead">Grade</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-center">Aksi</th>
                    </tr>

                </thead>

                @php
                    $ttlPcs = 0;
                    $ttlGr = 0;
                    foreach ($gudang as $d) {
                        $ttlPcs += $d->pcs;
                        $ttlGr += $d->gr;
                    }
                @endphp
                <tr>
                    <td class=" dheadstock h6">Total</td>
                    <td class="dheadstock"></td>
                    <td class="text-end dheadstock h6 ">{{ $ttlPcs }}</td>
                    <td class="text-end dheadstock h6 ">{{ $ttlGr }}</td>
                    <td class="dheadstock"></td>
                </tr>
                <tbody>
                    @foreach ($gudang as $d)
                        <tr>
                            <td>SP{{ $d->no_box }}</td>
                            <td class="text-primary pointer detail" data-nobox="{{ $d->no_box }}">{{ $d->grade }}
                            </td>
                            <td class="text-end">{{ $d->pcs }}</td>
                            <td class="text-end">{{ $d->gr }}</td>
                            <td align="right">
                                @php
                                    $param = ['no_box' => $d->no_box, 'selesai' => $d->selesai];
                                @endphp
                                @if ($d->selesai == 'T')
                                    <a onclick="return confirm('Yakin dihapus ?')"
                                        href="{{ route('gradingbj.cancel', $param) }}">
                                        <span class="badge bg-danger">Cancel</span>
                                    </a>
                                @endif

                                {{-- <a href="#" class="edit" data-no_invoice="{{ $d->no_invoice }}"
                                        data-kategori="cetak">
                                        <span class="badge bg-primary">Edit</span>
                                    </a> --}}
                                    
                                <a onclick="return confirm('Anda Yakin ?')"
                                    href="{{ route('gradingbj.selesai', $param) }}">
                                    <span class="badge bg-success">
                                        @if ($d->selesai == 'Y')
                                            <i class="fas fa-redo"></i>
                                        @else
                                            selesai
                                        @endif
                                    </span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <x-theme.modal title="Detail" idModal="detail" btnSave="T">
            <div class="loading d-none">
                <x-theme.loading />
            </div>
            <div id="load_detail"></div>
        </x-theme.modal>

        @section('scripts')
            <script>
                loadTable('tbl1')
                $('.detail').click(function(e) {
                    e.preventDefault();
                    const no_box = $(this).data('nobox')
                    $("#detail").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.detail') }}",
                        data: {
                            no_box,
                        },
                        beforeSend: function() {
                            $("#load_detail").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#load_detail").html(r);
                            loadTable('tblDetail')
                        }
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
