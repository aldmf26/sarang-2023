

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab"
            aria-controls="home" aria-selected="true">Selisih</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile"
            aria-selected="false" tabindex="-1">Susut</a>
    </li>
    
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="row">
            <div class="col-lg-12 mb-2">
                <a href="{{ route('gradingbj.export_selisih') }}" class="btn btn-primary btn-sm float-end"><i
                        class="fas fa-print"></i> Export</a>
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered table-striped" id="tblSelisih">
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
    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <div class="row">
            <div class="col-lg-12 mb-2">
                <a href="{{ route('gradingbj.export_susut') }}" class="btn btn-primary btn-sm float-end"><i
                    class="fas fa-print"></i> Export</a>
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered table-striped" id="tblSusut">
                    <thead>
                        <tr>
                            <th class="dhead">No Box</th>
                            <th class="dhead text-end">Susut %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($susut as $d)
                            <tr>
                                <td>{{ $d->no_box }}</td>
                                <td align="right">{{ number_format((1-($d->gr / $d->gr_awal)) * 100,0) }} %</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 
</div>
