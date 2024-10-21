<table id="tbl1" class="mt-2 table table-hover table-striped table-bordered">
    <thead>
        <tr>
            <th class="dhead">No Box Grading</th>
            <th class="dhead text-center">Grade</th>
            <th class="dhead text-end">Pcs</th>
            <th class="dhead text-end">Gr</th>
            {{-- <th class="dhead text-center">Detail</th> --}}
            <th class="dhead text-center">Aksi</th>
        </tr>

    </thead>

    @php
        $ttlPcs = 0;
        $ttlGr = 0;
        foreach ($gudang as $d) {
            if (
                $d->pcs - $d->pcs_pengiriman >= 0 &&
                $d->gr - $d->gr_pengiriman > 0
            ) {
                $ttlPcs += $d->pcs - $d->pcs_pengiriman;
                $ttlGr += $d->gr - $d->gr_pengiriman;
            }
        }
    @endphp
    <tr>
        <td class=" dheadstock h6">Total</td>
        <td class="dheadstock"></td>
        <td class="text-end dheadstock h6 ">{{ number_format($ttlPcs, 0) }}</td>
        <td class="text-end dheadstock h6 ">{{ number_format($ttlGr, 0) }}</td>

        <td class="dheadstock"></td>
    </tr>
    <tbody>
        @foreach ($gudang as $d)
            @if ($d->pcs - $d->pcs_pengiriman >= 0 && $d->gr - $d->gr_pengiriman > 0)
                <tr
                    @click="
        if (cek.includes('{{ $d->no_box }}')) {
            cek = cek.filter(x => x !== '{{ $d->no_box }}')
            ttlPcs -= {{ $d->pcs - $d->pcs_pengiriman }}
            ttlGr -= {{ $d->gr - $d->gr_pengiriman }}
        } else {
            cek.push('{{ $d->no_box }}')
            ttlPcs += {{ $d->pcs - $d->pcs_pengiriman }}
            ttlGr += {{ $d->gr - $d->gr_pengiriman }}
        }
    ">
                    <td>P{{ $d->no_box }}</td>
                    <td class="text-primary text-center pointer">
                        <span class="detail"
                            data-nobox="{{ $d->no_box }}">{{ $d->grade }}</span>
                    </td>
                    <td class="text-end">
                        {{ number_format($d->pcs - $d->pcs_pengiriman, 0) }}</td>
                    <td class="text-end">
                        {{ number_format($d->gr - $d->gr_pengiriman, 0) }}</td>

                    <td align="right" class="d-flex justify-content-evenly">
                        <input type="checkbox" class="form-check"
                            :checked="cek.includes('{{ $d->no_box }}')"
                            name="id[]" id=""
                            value="{{ $d->no_box }}">
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>