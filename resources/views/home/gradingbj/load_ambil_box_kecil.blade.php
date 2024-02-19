<style>
    .pointer {
        cursor: pointer;
    }
</style>
<section class="row">
    <div class="col-lg-12">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">Grade</th>
                    <th class="dhead">Pcs</th>
                    <th class="dhead">Gr</th>
                    <th class="dhead">Ttl Rp</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl">
                    </td>
                    <td>
                        <select class="selectGrade" name="" id="">
                            <option value="">Pilih Grade</option>
                            @foreach ($gudangbj as $d)
                                <option value="{{ $d->grade }}">{{ $d->grade }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control pcs_ambil" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control gr_ambil" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control ttlrp_ambil" readonly>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
