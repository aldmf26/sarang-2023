<div class="row">
    <div class="col-lg-6">
        <table class="table table-hover table-bordered" id="tableHarian">
            @php
                $bgDanger = 'text-white bg-danger';
                $buka = "<span class='badge bg-secondary float-end'>Buka <i class='fas fa-caret-down'></i></span>";
            @endphp
            <thead>
                <tr>
                    <td class="text-center dhead" colspan="7">SELESAI</td>
                </tr>
                <tr>
                    <th class="dhead">Pgws</th>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">Nama</th>
                    <th class="dhead">Kls</th>
                    <th class="dhead">Ttl Gaji</th>
                    <th class="dhead">Hari Masuk</th>
                    <th class="dhead">Rata2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nurul</td>
                    <td>1 Apr</td>
                    <td>Dupia</td>
                    <td>3</td>
                    <td>500,000</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-5">
        <table class="table table-hover table-bordered" id="tableHarian2">
            @php
                $bgDanger = 'text-white bg-danger';
                $buka = "<span class='badge bg-secondary float-end'>Buka <i class='fas fa-caret-down'></i></span>";
            @endphp
            <thead>
                <tr>
                    <td class="text-center dhead" colspan="7">PROSES</td>
                </tr>
                <tr>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">Nama</th>
                    <th class="dhead">Kls</th>
                    <th class="dhead">Ttl Gaji</th>
                    <th class="dhead">Hari Masuk</th>
                    <th class="dhead">Rata2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1 Apr</td>
                    <td>Dupia</td>
                    <td>3</td>
                    <td>300,000</td>
                    <td>1</td>
                    <td>30000</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

