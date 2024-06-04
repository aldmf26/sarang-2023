<style>
    .pointer {
        cursor: pointer;
    }
</style>
<section class="row" x-data="{
    baris: 1,
    initSelect2: function() {
        $('.selectGrade').select2({
            dropdownParent: $('#ambil_box_kecil .modal-content')
        })
    },
   


}" x-init="initSelect2">
    <div class="col-lg-12">
        <input type="hidden" value="{{ date('Y-m-d') }}" class="form-control" name="tgl">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                font-size: 12px;
                width: 120px !important;
            }
        </style>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="dhead">Grade</th>
                    <th class="dhead text-end" width="80">Pcs</th>
                    <th class="dhead text-end" width="80">Gr</th>
                    <th class="dhead text-end" width="150">Ttl Rp</th>
                </tr>
            </thead>
            <tbody>
                <tr>

                    <td>
                    
                        <select class="selectGrade" name="grade" id="">
                            <option value="">Pilih Grade</option>
                            @foreach ($gudangbj as $d)
                                <option value="{{ $d->grade }}">{{ $d->grade }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input name="pcsTtlAmbil" type="text" class="form-control text-end pcsTtl_ambil" readonly>
                    </td>
                    <td>
                        <input name="grTtlAmbil" type="text" class="form-control text-end grTtl_ambil" readonly>
                    </td>
                    <td>
                        <input name="ttlrpTtlAmbil" type="text" class="form-control text-end ttlrpTtl_ambil"
                            readonly>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <h6>Tambah Box Kecil</h6>
    <div class="col-lg-12">
        <div class="col-lg-2">
            baris :
            <input type="text" class="form-control form-control-sm mb-2" x-model="baris">
        </div>
        <table class="table table-striped table-bordered">
            <thead class="bg-success text-white">
                <tr>
                    <th class="">Grade</th>
                    <th class="">No Box</th>
                    <th class=" text-end">Pcs</th>
                    <th class=" text-end">Gr</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in Array.from({ length: baris })" :key="index + '-' + 1">
                    <tr>
                        <td>
                            <h6 class="gradeInput"></h6>
                        </td>
                        <td>
                            <input value="SP" type="text" class="form-control" name="no_box[]">
                        </td>
                        <td>
                            <input type="text" class="form-control pcsAmbil text-end" name="pcs[]">
                        </td>
                        <td>
                            <input type="text" class="form-control grAmbil text-end" name="gr[]">
                        </td>

                    </tr>
                </template>


            </tbody>
        </table>
    </div>
</section>
