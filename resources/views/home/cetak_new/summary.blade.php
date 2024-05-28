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
                <table id="tbl_summary" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Pgws</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead text-center">Hari Kerja</th>
                            <th class="dhead text-end">Rp Gaji</th>
                            <th class="dhead text-end">Rata-rata</th>
                            <th class="dhead text-center">Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $i => $d)
                            @php
                                $ttl_hari = $d->ttl_hari;
                                $ttl_rp =
                                    $d->ttl_rp +
                                    $d->ttl_rp_cabut +
                                    $d->ttl_rp_sortir +
                                    $d->ttl_rp_eo +
                                    $d->ttl_rp_dll -
                                    $d->denda;
                                $rata2 = $ttl_rp / $ttl_hari;
                                $target = 90000;
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $d->pgws }}</td>
                                <td><a href="#" class="detail"
                                        ttl_hari="{{$ttl_hari}}" id_anak="{{ $d->id_anak }}">{{ $d->nama }}</a></td>
                                <td align="right">{{ $ttl_hari }}</td>
                                <td align="right">
                                    {{ number_format($ttl_rp, 0) }}
                                </td>
                                <td align="right">{{ number_format($rata2, 0) }}</td>
                                <td>{{ $rata2 < $target ? 'Tidak' : '' }} Capai</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <x-theme.modal size="modal-lg" title="Detail History" btnSave="T" idModal="detail">
            <div id="load_detail"></div>
        </x-theme.modal>
        @section('scripts')
            <script>
                loadTable('tbl_summary')
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
