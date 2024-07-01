<div class="row">
    <div class="col-lg-12 mb-2">
        <a href="{{ route('gradingbj.export_selisih') }}" class="btn btn-primary btn-sm float-end"><i class="fas fa-print"></i> Export</a>
    </div>
    <div class="col-lg-12">
        <table class="table table-boreder" id="tblSelisih">
            <thead>
                <tr>
                    <th class="dhead">No Box</th>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">Pcs</th>
                    <th class="dhead">Gr</th>
                    <th class="dhead">Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($selisih as $d)
                    <tr>
                        <td>{{ $d->no_box }}</td>
                        <td>{{ tanggal($d->tgl) }}</td>
                        <td>{{ $d->pcs }}</td>
                        <td>{{ $d->gr }}</td>
                        <td>{{ $d->admin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>