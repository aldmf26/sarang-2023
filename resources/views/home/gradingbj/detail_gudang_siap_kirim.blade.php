<div class="row">
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
</div>
<div class="row">
    <div class="col-lg-12">
        <table id="tblDetail" class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="dhead">No Box Sortir</th>
                    <th class="dhead">Tipe</th>
                    <th class="dhead">Pcs</th>
                    <th class="dhead">Gr</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $d)
                    <tr>
                        <td>{{ $d->no_box_sortir }}</td>
                        <td>{{ $d->tipe . ' ' . $d->ket }}</td>
                        <td>{{ $d->pcs }}</td>
                        <td>{{ $d->gr }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>