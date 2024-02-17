<form action="{{ route('packinglist.tbh_invoice') }}" method="post">

    <div class="col-lg-12" x-data="{
        tbhInvoice: false
    }">
        <table class="table table-stripped" id="tablealdi">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tgl Kirim</th>
                    <th>No Packinglist</th>
                    <th>No Invoice</th>
                    <th>Nama Packing List</th>
                    <th class="text-end">Box</th>
                    <th class="text-end">Pcs</th>
                    <th class="text-end">Gr</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($packing as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ tanggal($d->tgl) }}</td>
                        <td>{{ $d->no_nota }}</td>
                        <td class="tambah_invoice" no_invoice="{{ $d->no_nota }}">
                            @if (!$d->no_invoice)
                                @csrf
                                <span @click="tbhInvoice = !tbhInvoice"
                                    class="badge bg-primary">Tambah</span>
                                <div x-show="tbhInvoice">
                                    <input style="width:80px;" type="text" name="no_invoice[]"
                                        class="mt-1 form-control form-control-sm">
                                    <input type="hidden" name="no_nota[]" value="{{ $d->no_nota }}">
                                    <button class="mt-1 btn btn-sm btn-primary" type="submit">Save</button>
                                </div>
                            @else
                                {{ $d->no_invoice }}
                            @endif

                        </td>
                        <td>{{ ucwords($d->nm_packing) }}</td>
                        <td align="right">{{ $d->ttl_box }}</td>
                        <td align="right">{{ number_format($d->pcs, 0) }}</td>
                        <td align="right">{{ number_format($d->gr, 0) }}</td>
                        <td align="center">
                            <button class="btn btn-sm btn-primary detail" no_nota="{{ $d->no_nota }}"
                                type="button"><i class="fas fa-eye"></i></button>
                            <a href="{{ route('packinglist.print', $d->no_nota) }}"
                                class="btn btn-sm btn-primary" target="_blank"><i
                                    class="fas fa-print"></i></a>
                            <a onclick="return confirm('Yakin dihapus ?')"
                                href="{{ route('packinglist.delete', $d->no_nota) }}"
                                class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</form>