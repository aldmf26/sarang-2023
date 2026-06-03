<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <section class="row" x-data="{
            bulkBoxChecklist: false,
            bulkNoBoxInput: '',
            cek: [],
            boxData: {
                @foreach ($formulir as $d)
                    '{{ $d->no_box }}': { pcs: {{ $d->pcs_awal }}, gr: {{ $d->gr_awal }} }, @endforeach
            },
            totalPcs: 0,
            totalGr: 0,
            applyBulkSelect() {
                const values = this.bulkNoBoxInput
                    .split(',')
                    .map(value => value.trim())
                    .filter(value => value.length);
                this.cek = Array.from(new Set(values));
                this.updateTotals();
            },
            updateTotals() {
                this.totalPcs = 0;
                this.totalGr = 0;
                this.cek.forEach(box => {
                    if (this.boxData[box]) {
                        this.totalPcs += this.boxData[box].pcs;
                        this.totalGr += this.boxData[box].gr;
                    }
                });
            }
        }" @change="updateTotals()">
            <div class="col-lg-12">
                <x-theme.alert pesan="{{ session()->get('error') }}" />
            </div>
            <div class="col-lg-12 mt-2">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text" id="tbl1input" class="form-control form-control-sm mb-2"
                            placeholder="cari">
                    </div>

                    <div class="col-lg-8">
                        <form action="{{ route('gradingbj.grading_partai') }}" method="post">
                            @csrf
                            {{-- <a data-bs-toggle="modal" data-bs-target="#import" class="btn btn-sm btn-primary"
                                href="">Import</a> --}}
                            {{--
                            <a href="#" data-bs-target="#selisih" data-bs-toggle="modal"
                                class="selisih btn btn-sm btn-primary" href=""><i class="fa fa-warehouse"></i>
                                Data Selisih</a>
                            <button type="submit" name="submit" value="export" class="btn btn-sm btn-primary"
                                href="" x-show="cek.length">Export</button> --}}



                            <input type="hidden" name="no_box" class="form-control" :value="cek">

                            {{-- <button name="submit" value="grading" x-transition x-show="cek.length"
                                class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-plus"></i>
                                Grading
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button> --}}

                            <a href="{{ route('gradingbj.gudang') }}" style="color: white;background-color: #D722A9;"
                                class="btn btn-sm ">
                                <i class="fas fa-clipboard-list"></i> Gudang
                            </a>

                            <x-theme.button
                                href="{{ route('gudangsarang.invoice_grading', ['kategori' => 'grading']) }}"
                                icon="fa-clipboard-list" teks="Po Grading" />

                            <button name="submit" value="serah" x-transition x-show="cek.length"
                                class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-plus"></i>
                                Serah
                                <span x-text="cek.length"></span> Box |
                                <span x-text="`${totalPcs.toLocaleString('id-ID')} Pcs`"></span> |
                                <span x-text="`${totalGr.toLocaleString('id-ID')} Gr`"></span>
                            </button>
                            {{-- <button name="submit" value="selisih" x-transition x-show="cek.length"
                                class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-plus"></i>
                                Selisih
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button> --}}
                        </form>
                    </div>
                </div>
                <button class="btn btn-xs btn-primary" @click="bulkBoxChecklist = !bulkBoxChecklist">Bulk No box
                    Checklist</button>
                <div class="row mb-2 mt-2">
                    <div class="col-lg-8" x-show="bulkBoxChecklist" x-transition>
                        <input type="text" x-model="bulkNoBoxInput" class="form-control form-control-sm"
                            placeholder="Masukkan no box, pisahkan dengan koma">
                    </div>
                    <div class="col-lg-4 d-flex gap-2" x-show="bulkBoxChecklist" x-transition>
                        <button x-show="bulkBoxChecklist" type="button" @click="applyBulkSelect()"
                            class="btn btn-sm btn-primary">Apply
                            Bulk</button>
                        <button x-show="bulkBoxChecklist" type="button"
                            @click="bulkNoBoxInput=''; cek=[]; updateTotals()"
                            class="btn btn-sm btn-secondary">Clear</button>
                    </div>
                </div>
                <div style="overflow-y: scroll; height: 500px">
                    <table id="tbl1" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                {{-- <th class="dhead">Tanggal</th> --}}
                                <th class="dhead">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th width="90" class="dhead">Tipe - Ket</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                @role('presiden')
                                    <th class="dhead text-end">Rp/Gr</th>
                                    <th class="dhead text-end">Ttl Rp</th>
                                @endrole
                                <th class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formulir as $i => $d)
                                <tr class=""
                                    @click="cek.includes('{{ $d->no_box }}') ? cek = cek.filter(x => x !== '{{ $d->no_box }}') : cek.push('{{ $d->no_box }}'); updateTotals()">
                                    <td>{{ $i + 1 }}</td>
                                    {{-- <td>{{ tanggal($d->tanggal) }}</td> --}}
                                    <td>{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td>{{ $d->tipe . ' - ' . $d->ket }}</td>
                                    <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                                    @role('presiden')
                                        <td class="text-end">
                                            {{-- {{ number_format(($d->cost_bk + $d->cost_cbt + $d->cost_eo + $d->cost_ctk + $d->cost_str + $d->cost_cu) / ($d->gr_awal ?? 1), 0) }} --}}
                                        </td>
                                        <td class="text-end">
                                            {{-- {{ number_format($d->cost_bk + $d->cost_cbt + $d->cost_eo + $d->cost_ctk + $d->cost_str + $d->cost_cu, 0) }} --}}
                                        </td>
                                    @endrole
                                    <td align="center">
                                        <input type="checkbox" class="form-check" x-model="cek"
                                            @change="updateTotals()" name="id[]" value="{{ $d->no_box }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </section>
        <x-theme.import title="Import grading" route="gradingbj.import" routeTemplate="gradingbj.template_import" />

        <x-theme.modal btnSave="T" title="Data Selisih" idModal="selisih">
            <div id="loadSelisih"></div>
        </x-theme.modal>

        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
                $(".selisih").click(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.load_selisih') }}",
                        success: function(r) {
                            $("#loadSelisih").html(r);
                            loadTable('tblSelisih')
                            loadTable('tblSusut')

                        }
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
