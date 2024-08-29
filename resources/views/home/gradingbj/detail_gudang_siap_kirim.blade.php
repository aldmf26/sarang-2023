{{-- <div class="row">
    <div class="col-lg-6">
        <table class="table">
            <tr>
                <th>No Box Grade</th>
                <th>:</th>
                <th class="h6">SP{{ $detail[0]->no_box }}</th>
            </tr>
            <tr>
                <th>Grade</th>
                <th>:</th>
                <th class="h6">{{ $detail[0]->grade }}</th>
            </tr>
        </table>
    </div>
</div> --}}
<div class="row">
    <div class="col-lg-12">
        <table id="tblDetail" class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="dhead">Nama Partai</th>
                    <th class="dhead">Invoice</th>
                    <th class="dhead text-center">Grade</th>
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Gr</th>
                    <th class="dhead text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $d)
                    <tr>
                        <td>{{ $d->nm_partai }}</td>
                        <td>{{ $d->no_invoice }}</td>
                        <td align="center">{{ $d->grade }}</td>
                        <td align="right">{{ $d->pcs }}</td>
                        <td align="right">{{ $d->gr }}</td>
                        <td align="center">
                            <form action="{{ route('gradingbj.cancel') }}" method="post">
                                @csrf
                                @method('post')
                                <input type="hidden" name="no_invoice" value="{{ $d->no_invoice }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin di cancel ?')">
                                    Cancel
                                </button>
                            </form>
                        </td>
                        {{-- <td>
                            <form action="{{ route('gradingbj.cancel') }}" method="post">
                                @csrf
                                @method('post')
                                <input type="hidden" name="no_box" value="{{ $d->no_box_sortir }}">
                                <input type="hidden" name="selesai" value="{{ $d->selesai }}">
                                <input type="hidden" name="id_grading" value="{{ $d->id_grading }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin di cancel ?')">
                                    <span class="badge bg-danger">Cancel</span>
                                </button>
                            </form>
                        </td> --}}
                       
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
