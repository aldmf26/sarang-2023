<x-theme.app title="{{ $title }} " table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}
                {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}
                <span class="text-warning" style="font-size: 12px"><em>jika data tidak ada silahkan view dulu
                        !</em></span>
            </h6>
            <div>
                @include('home.cabut.view_bulandibayar')
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <div class="row">
            <div class="col">
                <table id="tblHistory" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead text-end">Ttl Hari</th>
                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Gr Awal</th>
                            <th class="dhead text-end">Pcs Akhir</th>
                            <th class="dhead text-end">Gr Akhir</th>
                            <th class="dhead text-end">Ttl Rp</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ strtoupper($d->nama) }} / {{ $d->kelas }}</td>
                                <td align="right">{{ $d->ttl_hari }}</td>
                                <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                <td align="right">{{ number_format($d->pcs_akhir, 0) }}</td>
                                <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                <td><button id_anak="{{ $d->id_anak }}" ttl_hari="{{ $d->ttl_hari }}" class="btn btn-sm btn-primary detail"><i
                                            class="fas fa-eye"></i></button></td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="dhead text-center">TOTAL</th>
                            <th class="dhead text-end">{{ number_format($pcs_awal, 0) }}</th>
                            <th class="dhead text-end">{{ number_format($gr_awal, 0) }}</th>
                            <th class="dhead text-end">{{ number_format($pcs_akhir, 0) }}</th>
                            <th class="dhead text-end">{{ number_format($gr_akhir, 0) }}</th>
                            <th class="dhead text-end">{{ number_format($ttl_rp, 0) }}</th>
                            <th class="dhead"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <x-theme.modal size="modal-lg" title="Detail History" btnSave="T" idModal="detail">
            <div id="load_detail"></div>
        </x-theme.modal>
        @section('scripts')
            <script>
                loadTable('tblHistory')
                $('.detail').click(function(e) {
                    e.preventDefault();
                    const id_anak = $(this).attr("id_anak")
                    const ttl_hari = $(this).attr("ttl_hari")
                    const bulan = "{{ $bulan }}"
                    const tahun = "{{ $tahun }}"
                    $('#detail').modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cetaknew.history_detail') }}",
                        data: {
                            id_anak,
                            bulan,
                            ttl_hari,
                            tahun
                        },
                        success: function(r) {
                            $("#load_detail").html(r);
                            loadTable('tblDetail')
                        }
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
