<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>

    </x-slot>

    <x-slot name="cardBody">

        <section x-data="{
            cek: [],
            cekPrint: [],
            ttlPcs: 0,
            ttlGr: 0
        }">
            <div class="row">
                <form action="{{ route('qc.po_wip') }}" method="post">
                    @csrf
                    <div class="col-lg-12 mb-4">
                        <input type="hidden" name="no_box" class="form-control"
                            :value="cek.concat(cekPrint).join(',')">
                        <button value="kirim" x-transition x-show="cek.length"
                            class="btn btn-sm btn-primary  float-end ms-2" name="submit">
                            <i class="fas fa-plus"></i>
                            Wip2
                            <span class="badge bg-white text-black" x-text="cek.length" x-transition></span>
                            <span x-transition><span x-text="ttlPcs"></span> Pcs <span x-text="ttlGr"></span> Gr</span>
                        </button>

                        <a href="{{ route('gudangsarang.invoice_wip2', ['kategori' => 'wip2']) }}"
                            class="btn btn-primary btn-sm  float-end ms-2">Po Wip2</a>
                        <a href="{{ route('qc.index') }}" class="btn btn-secondary btn-sm  float-end">Kembali</a>
                    </div>
                </form>
                <div class="col-lg-12">
                    <br>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No Invoice Qc</th>
                                <th class="dhead text-center">Ttl Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Gr Akhir</th>
                                <th class="dhead text-end">Susut%</th>
                                <th class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qc as $q)
                                <tr
                                    @click="
                                if (cek.includes('{{ $q->invoice_qc }}')) {
                                    cek = cek.filter(x => x !== '{{ $q->invoice_qc }}')
                                    ttlPcs -= {{ $q->pcs }}
                                    ttlGr -= {{ $q->gr }}
                                } else {
                                    cek.push('{{ $q->invoice_qc }}')
                                    ttlPcs += {{ $q->pcs }}
                                    ttlGr += {{ $q->gr }}
                                }">
                                    <td>QC-{{ $q->invoice_qc }}</td>
                                    <td class="text-center">
                                        {{ $q->total_box }}</td>
                                    <td class="text-end">{{ number_format($q->pcs, 0) }}</td>
                                    <td class="text-end">{{ number_format($q->gr, 0) }}</td>
                                    <td class="text-end">{{ number_format($q->gr_akhir, 0) }}</td>
                                    <td class="text-end">
                                        {{ empty($q->gr_akhir) ? 0 : number_format((1 - $q->gr_akhir / $q->gr) * 100, 0) }}
                                        %
                                    </td>
                                    <td class="text-center">
                                        @if ($q->selesai == 'Y')
                                            <center>
                                                <input type="checkbox" class="form-check"
                                                    :checked="cek.includes('{{ $q->invoice_qc }}')" name="id[]"
                                                    id="" value="{{ $q->invoice_qc }}">
                                            </center>
                                        @else
                                            <a href="{{ route('qc.listboxqc', ['invoice_qc' => $q->invoice_qc]) }}"
                                                class="btn btn-primary btn-sm">Qc</a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </section>



    </x-slot>

</x-theme.app>
