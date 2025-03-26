<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <section x-data="{
            cek: [],
            cekPrint: [],
            ttlPcs: 0,
            ttlGr: 0
        }">
            <div class="row">
                <div class="col-lg-4">
                    <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                </div>

                <div class="col-lg-8">
                    <form action="{{ route('qc.save_invoice_qc') }}" method="post">
                        @csrf

                        <a href="{{ route('qc.listqc') }}" class="btn btn-sm btn-info" href=""><i
                                class="fas fa-clipboard-list"></i> List
                            Qc</a>
                        <a href="{{ route('gudangsarang.invoice_qc', ['kategori' => 'qc']) }}"
                            class="btn btn-sm btn-info" href=""><i class="fa fa-warehouse"></i> Po Wip2</a>
                        <input type="hidden" name="no_box" class="form-control"
                            :value="cek.concat(cekPrint).join(',')">

                        <button value="kirim" x-transition x-show="cek.length" class="btn btn-sm btn-primary"
                            name="submit">
                            <i class="fas fa-plus"></i>
                            QC
                            <span class="badge bg-white text-black" x-text="cek.length" x-transition></span>
                            <span x-transition><span x-text="ttlPcs"></span> Pcs <span x-text="ttlGr"></span> Gr</span>
                        </button>

                    </form>

                </div>
                <div class="scrollable-table col-lg-12">
                    <table id="tbl1" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No Box Grading</th>
                                <th class="dhead text-center">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-center">Qc</th>
                            </tr>

                        </thead>
                        <tr>
                            <td class=" dheadstock h6">Total</td>
                            <td class="dheadstock"></td>
                            <td class="text-end dheadstock h6 ">{{ number_format(sumBk($qc, 'pcs_awal'), 0) }}</td>
                            <td class="text-end dheadstock h6 ">{{ number_format(sumBk($qc, 'gr_awal'), 0) }}</td>
                            <td class="dheadstock"></td>
                        </tr>
                        <tbody>
                            @foreach ($qc as $d)
                                <tr
                                    @click="
                                                if (cek.includes('{{ $d->box_pengiriman }}')) {
                                                    cek = cek.filter(x => x !== '{{ $d->box_pengiriman }}')
                                                    ttlPcs -= {{ $d->pcs_awal }}
                                                    ttlGr -= {{ $d->gr_awal }}
                                                } else {
                                                    cek.push('{{ $d->box_pengiriman }}')
                                                    ttlPcs += {{ $d->pcs_awal }}
                                                    ttlGr += {{ $d->gr_awal }}
                                                }">

                                    <td>{{ $d->box_pengiriman }}</td>
                                    <td class="text-center">
                                        {{ $d->grade }}</td>
                                    <td class="text-end">{{ $d->pcs_awal }}</td>
                                    <td class="text-end">{{ $d->gr_awal }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check"
                                            :checked="cek.includes('{{ $d->box_pengiriman }}')" name="id[]"
                                            id="" value="{{ $d->box_pengiriman }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <x-theme.modal title="Detail" idModal="detail" btnSave="T">
            <div class="loading d-none">
                <x-theme.loading />
            </div>
            <div id="load_detail"></div>
        </x-theme.modal>

        <x-theme.import title="Import Pengiriman" route="gradingbj.import_gudang_siap_kirim"
            routeTemplate="gradingbj.template_import_gudang_siap_kirim" />

        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')

                $('.detail').click(function(e) {
                    e.preventDefault();
                    const no_box = $(this).data('nobox')
                    $("#detail").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.detail_perpartai') }}",
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
