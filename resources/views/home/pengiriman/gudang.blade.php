<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>

            <div>
                {{-- <a href="{{ route('sortir.export_gudang') }}" class="btn btn-sm btn-primary"><i
                        class="fas fa-file-excel"></i> Export
                    All</a>
                <x-theme.button href="#" icon="fa-plus" variant="info" modal="Y" idModal="tambah"
                    teks="serah" />
                <x-theme.button href="{{ route('gudangsarang.invoice_grade', ['kategori' => 'grade']) }}"
                    icon="fa-clipboard-list" teks="Po Grade" /> --}}
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-5">
                <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                <table id="tbl1" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="7">Belum Kirim</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Grade</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Rp/gr</th>
                            <th class="dhead text-end">Total Rp</th>
                        </tr>
                        @php
                            if (!function_exists('ttl')) {
                                function ttl($tl, $kolom)
                                {
                                    return array_sum(array_column($tl, $kolom));
                                }
                            }
                            $ttlRpGudang = 0;
                            foreach($gudang as $g) {
                                    $ttlRpGudang += $g->total_rp_gram_str * $g->gr;
                            }
                        @endphp
                        <tr>
                            <th class="dheadstock text-center" colspan="2">Total</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($gudang,'pcs'),0) }}</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($gudang,'gr'),0) }}</th>
                            <th class="dheadstock text-end"></th>
                            <th class="dheadstock text-end">{{ number_format($ttlRpGudang,0)  }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudang as $d)
                        @if ($d->pcs - $d->pcs_pengiriman >= 0 && $d->gr - $d->gr_pengiriman >= 0)
                        @php
                            $rpGram = $d->total_rp_gram_str;
                        @endphp
                            <tr >
                                <td>P{{ $d->no_box }}</td>
                                <td class="">   
                                    <span class="detail" data-nobox="{{ $d->no_box }}">{{ $d->grade }}</span>  
                                </td>
                                <td class="text-end">{{ $d->pcs }}</td>
                                <td class="text-end">{{ $d->gr }}</td>
                                <td class="text-end">{{ number_format($rpGram,0) }}</td>
                                <td class="text-end">{{ number_format($rpGram * $d->gr,0) }}</td>
                            </tr>
                        @endif
                    @endforeach
                        
                    </tbody>
                </table>
            </div>
            <div class="col-lg-5">
                <input type="text" id="tbl2input" class="form-control form-control-sm mb-2" placeholder="cari">
                <table id="tbl2" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="7">Selesai Kirim</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Grade</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Rp/gr</th>
                            <th class="dhead text-end">Total Rp</th>
                        </tr>
                        @php
                        
                        $ttlRpGudang = 0;
                        $pcsTtl = 0;
                        $grTtl = 0;
                        foreach($selesai as $g) {
                            $pcsTtl += $g->pcs;
                            $grTtl += $g->gr;
                            $ttlRpGudang += $g->rp_gram * $g->gr;
                        }
                    @endphp
                    <tr>
                        <th class="dheadstock text-center" colspan="2">Total</th>
                        <th class="dheadstock text-end">{{ number_format($pcsTtl,0) }}</th>
                        <th class="dheadstock text-end">{{ number_format($grTtl,0) }}</th>
                        <th class="dheadstock text-end"></th>
                        <th class="dheadstock text-end">{{ number_format($ttlRpGudang,0)  }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($selesai as $d)
                        @php
                            $rpGram = $d->rp_gram;
                        @endphp
                            <tr >
                                <td>P{{ $d->no_box }}</td>
                                <td class="">   
                                    <span class="detail" data-nobox="{{ $d->no_box }}">{{ $d->grade }}</span>  
                                </td>
                                <td class="text-end">{{ $d->pcs }}</td>
                                <td class="text-end">{{ $d->gr }}</td>
                                <td class="text-end">{{ number_format($rpGram,0) }}</td>
                                <td class="text-end">{{ number_format($rpGram * $d->gr,0) }}</td>
                            </tr>
                    @endforeach
                        
                    </tbody>
                </table>
            </div>
            <x-theme.modal title="Detail" idModal="detail" btnSave="T">
                <div class="loading d-none">
                    <x-theme.loading />
                </div>
                <div id="load_detail"></div>
            </x-theme.modal>
        </div>
        @section('scripts')
            <script>
                ["tbl1", "tbl2", "tbl3"].forEach((tbl, i) => pencarian(`tbl${i+1}input`, tbl));
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
