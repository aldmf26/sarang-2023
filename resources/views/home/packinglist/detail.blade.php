<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="sum-tab" data-bs-toggle="tab" href="#sum" role="tab" aria-controls="home" aria-selected="true">Summary</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="list-tab" data-bs-toggle="tab" href="#list" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">List Grade</a>
    </li>
    <li class="nav-item ms-auto">
        <a href="{{route('pengiriman.po_export', $no_nota)}}" class="btn btn-primary btn-sm"><i class="fas fa-file-excel"></i>Export</a>
    </li>

</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade active show" id="sum" role="tabpanel" aria-labelledby="sum-tab">
        <input id="pencarianSum" type="text" class="form-control form-control-sm mb-2" placeholder="pencarian...">
        @include('home.packinglist.tbl_sum_pengiriman')
    </div>
    <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
        <input id="pencarianList" type="text" class="form-control form-control-sm mb-2" placeholder="pencarian...">
        @include('home.packinglist.tbl_list_pengiriman')
    </div>

</div>


