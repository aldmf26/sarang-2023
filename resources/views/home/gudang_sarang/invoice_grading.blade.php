<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">Po Grading</h6>
            <div>
                <x-theme.btn_filter />
            </div>
        </div>

        @include('home.gudang_sarang.nav')
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table table-bordered" id="nanda">
                <thead>
                    <tr>
                        <th class="dhead" width="5">#</th>
                        <th class="dhead">Tanggal</th>
                        <th class="dhead">No PO</th>
                        <th class="dhead">Nama Pemberi</th>
                        <th class="dhead">Nama Penerima</th>
                        <th class="dhead text-center">Ttl Box</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulir as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($d->tanggal) }}</td>
                            <td>
                                {{ $d->no_invoice }}
                            </td>
                            <td>{{ $d->pemberi }}</td>
                            <td>{{ $d->penerima }}</td>
                            <td align="right">{{ $d->ttl_box }}</td>
                            <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($d->gr, 0) }}</td>
                            <td>
                                @php

                                    $no_invoice = $d->no_invoice;
                                    $getBox = DB::table('formulir_sarang')
                                        ->where([['no_invoice', $no_invoice], ['kategori', 'grading']])
                                        ->pluck('no_box')
                                        ->toArray();
                                    $hasil = implode(',', $getBox);

                                    $getSudahGrading = DB::table('grading')->where('no_invoice', $no_invoice)->first();
                                @endphp
                                @presiden
                                @pgwsGrading
                                    @if ($getSudahGrading)
                                    <a href="{{ route('gradingbj.detail_pengiriman', ['no_invoice' => $d->no_invoice]) }}">
                                        <span class="badge bg-primary">Detail</span>
                                    </a>
                                    @else
                                    <a onclick="return confirm('Yakin dihapus ?')"
                                        href="{{ route('gudangsarang.batal_grading', ['no_invoice' => $d->no_invoice, 'kategori' => 'grading']) }}">
                                        <span class="badge bg-danger">Cancel</span>
                                    </a>

                                    <a href="{{ route('gudangsarang.print_formulir_grading', ['no_invoice' => $d->no_invoice]) }}"
                                        target="_blank">
                                        <span class="badge bg-primary">Print</span>
                                    </a>
                                    <a href="{{ route('gradingbj.grading_partai_result', ['no_box' => $hasil,'no_invoice' => $d->no_invoice]) }}">
                                        <span class="badge bg-primary">Grading</span>
                                    </a>
                                    @endif
                                    {{-- <form method="POST" action="{{ route('gradingbj.grading_partai', ['no_box' => $hasil]) }}"
                                        onsubmit="return confirm('Yakin digrading ?')">
                                        @csrf
                                        <input type="hidden" name="no_box" value="{{ $hasil }}">
                                        <button type="submit" name="submit" class="border-0 badge bg-success" value="grading">Grading</button>
                                    </form> --}}
                                @endpgwsGrading
                                @endpresiden

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </section>
    </x-slot>

</x-theme.app>
