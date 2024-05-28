<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @media print {
          .form-check {
            display: none;
          }
        }
        .dhead {
            background-color: #435EBE !important;
            color: white;
        }
      </style>
</head>

<body class="py-3" x-data="{
    hide: true,
    classTh: 'align-middle text-center',
}">
    <h6 class="text-center"><u>SLIP GAJI</u></h6>
    <div class="form-check float-end me-5">
        {{-- <input @change="hide = ! hide" class="form-check-input" type="checkbox" value=""
            id="cekbox">
        <label class="form-check-label" for="cekbox">
            Sembunyikan data
        </label> --}}
        <button class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
    </div>
    <div class="px-3">
        <div class="row">
            <div class="col-lg-4">
                <table class="table">
                    <tr>
                        <th width="1">Pgws</th>
                        <th width="1">:</th>
                        <th>
                            {{ auth()->user()->name }}
                        </th>
                    </tr>
                    <tr>
                        <th width="1">Nama</th>
                        <th width="1">:</th>
                        <th>{{ $detail[0]->nm_anak }}</th>
                    </tr>
                    <tr>
                        <th width="1">Kelas</th>
                        <th width="1">:</th>
                        <th>{{ $detail[0]->id_kelas }}</th>
                    </tr>
                    {{-- <tr>
                        <th width="100">Hari Masuk</th>
                        <th width="1">:</th>
                        <th>{{ $ttl_hari }}</th>
                    </tr> --}}
                </table>
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered table-hover" id="tblDetail">
                    <thead>
                        <tr>
                            {{-- <th class="dhead">#</th> --}}
                            <th class="dhead">Tgl</th>
                            <th class="dhead">No Box</th>
                            <th class="dhead">Ket</th>
                            <th class="dhead text-end">pcs awal</th>
                            <th class="dhead text-end">gr awal</th>
                            <th class="dhead text-end">pcs akhir</th>
                            <th class="dhead text-end">gr akhir</th>
                            <th class="dhead text-end">sst%</th>
                            <th class="dhead text-end">Ttl Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $categories = [
                                'cabut' => 'Cbt',
                                'sortir' => 'Sortir',
                                'eo' => 'Eo',
                                'detail' => 'Ctk',
                                'dll' => 'Dll',
                                'denda' => 'Denda',
                            ];
                        @endphp
        
                        @foreach ($categories as $category => $label)
                            @foreach ($$category as $c)
                                @php
                                    $no++;
                                    $isDenda = $label == 'Denda';
                                @endphp
                                <tr @if ($isDenda) class="text-danger" @endif>
                                    {{-- <td>{{ $no }}</td> --}}
                                    <td>{{ tglFormat($c->tgl) }}</td>
                                    <td>{{ $category != 'dll' && $category != 'denda' ? $c->no_box : '-' }}</td>
                                    <td>{{ $label }}</td>
        
                                    @if ($category == 'eo')
                                        <td class="text-end">-</td>
                                    @else
                                        <td class="text-end">{{ $c->pcs_awal ?? '-' }}</td>
                                    @endif
        
                                    <td class="text-end">{{ $c->gr_awal ?? '-' }}</td>
        
                                    @if ($category == 'eo')
                                        <td class="text-end">-</td>
                                    @else
                                        <td class="text-end">{{ $c->pcs_akhir ?? '-' }}</td>
                                    @endif
        
                                    <td class="text-end">{{ $c->gr_akhir ?? '-' }}</td>
        
                                    <td class="text-end">
                                        @if ($category == 'detail')
                                            {{ empty($c->gr_awal_ctk) ? 0 : number_format((1 - $c->gr_akhir / $c->gr_awal_ctk) * 100, 1) }}%
                                        @else
                                            {{ number_format($c->susut, 1) ?? '-' }}%
                                        @endif
                                    </td>
        
                                    <td class="text-end">
                                        @if ($isDenda)
                                            {{ number_format($c->denda,0) }}
                                        @else
                                            {{ number_format($category == 'detail' ? $c->pcs_akhir * $c->rp_satuan : $c->ttl_rp) }}
                                        @endif
                                    </td>
                                </tr>
        
                            
                            @endforeach
                        @endforeach
                    </tbody>
        
                    <tfoot>
                        <tr>
                            <th class="dhead text-center" colspan="3">TOTAL</th>
                            <th class="text-end dhead">{{ number_format($ttlpcs_awal, 0) }}</th>
                            <th class="text-end dhead">{{ number_format($ttlgr_awal, 0) }}</th>
                            <th class="text-end dhead">{{ number_format($ttlpcs_akhir, 0) }}</th>
                            <th class="text-end dhead">{{ number_format($ttlgr_akhir, 0) }}</th>
                            <th class="text-end dhead"></th>
                            <th class="text-end dhead">{{ number_format($ttlttl_rp, 0) }}</th>
                        </tr>
                        <tr>
                            <th class="dhead text-end" colspan="8">Masuk Kerja : {{ $ttl_hari }}hari ~ Rata2 </th>
                            <th class="text-end dhead">{{ number_format($ttlttl_rp / $ttl_hari, 0) }}</th>
                        </tr>
                        {{-- <tr>
                            <th class="dhead text-end" colspan="4">Hari Masuk</th>
                            <th class="dhead">{{ number_format($ttlttl_rp / $ttl_hari,0) }}</th>
                        </tr> --}}
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

   
</body>

</html>
