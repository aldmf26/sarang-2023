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
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Gr</th>
                    <th class="dhead text-end">Ttl Rp</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl">
                    </td>
                    <td>
                        <select class="selectGrd" name="" id="">
                            <option value="">Pilih Grade</option>
                            @foreach ($gudangbj as $d)
                                <option value="{{ $d->grade }}">{{ $d->grade }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control text-end pcs_ambil" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control text-end gr_ambil" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control text-end ttlrp_ambil" readonly>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <h6>Tambah Box Kecil</h6>
    <div class="col-lg-8" x-data="{
        baris: 1,
        initSelect2: function() {
            $('.selectGrd').select2({
                dropdownParent: $('#ambil_box_kecil .modal-content')
            })
        }
    }">
        <div class="col-lg-2">
            baris :
            <input type="text" class="form-control form-control-sm mb-2" x-model="baris">
        </div>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="dhead">No Box</th>
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Gr</th>
                    <th class="dhead">Pengawas</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in Array.from({ length: baris })" :key="index + '-' + 1">
                    <tr>
                    <td>
                        <span x-text="index"></span>
                        <input type="text" class="form-control" name="no_box[]">
                    </td>
                    <td>
                        <input type="text" class="form-control text-end" name="pcs[]">
                    </td>
                    <td>
                        <input type="text" class="form-control text-end" name="gr[]">
                    </td>
                    <td>
                        <select name="pengawas[]" class="selectGrd" x-init="initSelect2" id="">
                            <option value="">Pilih pgws</option>
                            @foreach ($pengawas as $p)
                                <option value="{{ $p->id }}">{{ strtoupper($p->name) }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                </template>
          
                
            </tbody>
        </table>
    </div>
</section>
