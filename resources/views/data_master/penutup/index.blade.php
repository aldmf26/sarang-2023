<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">

        <h6 class="float-start mt-1">{{ $title }}</h6>
        {{-- @include('home.cabut.view_bulandibayar') --}}
        <button data-bs-toggle="modal" data-bs-target="#tutupGaji" class="btn btn-sm btn-primary float-end"><i
                class="fas fa-dollar-sign"></i>Tutup Gaji</button>

        <form onsubmit="return confirm('Tutup Gaji Bulan {{ formatTglGaji($bulan, $tahun) }}?')"
            action="{{ route('penutup.tutup_gaji') }}" method="post" id="formTutup">
            @csrf
            <x-theme.modal btnSave="{{ $cekTutup ? 'T' : 'Y' }}" title="Tutup Gaji" idModal="tutupGaji">
                @if ($cekTutup == 'Y')
                    <h6><em>Gaji Bulan : {{ formatTglGaji($bulan, $tahun) }} SUDAH DITUTUP</em></h6>
                @else
                    <h6 class="text-warning"><em>Tutup Gaji Bulan : {{ formatTglGaji($bulan, $tahun) }}</em></h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">Pgws</th>
                                <th class="dhead">Lokasi</th>
                                <th class="dhead text-end">Ttl Rp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlRp = 0;
                            @endphp
                            @foreach ($datas as $pgws => $d)
                                @if ($d['ttlRp'] > 0)
                                    <tr>
                                        <td>{{ $d['pgws'] }}</td>
                                        <td>{{ $d['lokasi'] }}</td>
                                        <td align="right" class="h6">{{ number_format($d['ttlRp'], 0) }}</td>
                                    </tr>
                                    @php
                                        $ttlRp += $d['ttlRp'];
                                    @endphp
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary text-white">
                                <th>Total</th>
                                <th></th>
                                <th class="text-white text-end h6">{{ number_format($ttlRp, 0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                @endif

            </x-theme.modal>
        </form>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Bulan</th>
                        {{-- <th>Pcs Akhir</th> --}}
                        {{-- <th>Gr Akhir</th> --}}
                        <th>Ttl Rp</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gaji as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ formatTglGaji($d->bulan_dibayar, $d->tahun_dibayar) }}</td>
                            <td class="h6">{{ number_format($d->ttl_gaji, 0) }}</td>
                            @php
                                $param = [$d->bulan_dibayar, $d->tahun_dibayar];
                            @endphp
                            <td><a href="{{ route('penutup.show', $param) }}" class="btn btn-sm btn-primary"><i
                                        class="fas fa-eye"></i> View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    </x-slot>
</x-theme.app>
