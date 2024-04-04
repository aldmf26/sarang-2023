<x-theme.app sizeCard="12" title="{{ $title }}">
    <x-slot name="cardHeader">

        @include('home.gradingbj.button_nav')

    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-5 mb-2">
                @include('home.gudang_tampilan.bjGradingAwal')
            </div>
            <div class="col-lg-5 mb-2">
                @include('home.gudang_tampilan.bjSortirProses')
            </div>
            <div class="col-lg-12">
                <hr>
            </div>
            <div class="col-lg-12 mb-3">
                @include('home.gradingbj.nav')
            </div>
            <form method="post" action="{{ route('gradingbj.create_suntikan_boxsp') }}" x-data="{
                baris: 1,
                suntikan: false,
                initSelect2: function() {
                    $('.selectPgws').select2();
                }
            }"
                x-init="initSelect2()">
                @csrf
                <div class="col-lg-3">
                    <h6 @click.prevent="suntikan = ! suntikan" class="btn btn-sm btn-primary">Tambah Suntikan <i
                            class="fas fa-plus"></i></h6>
                </div>
                <div x-show="suntikan" class="col-lg-12">
                    <table class="mb-2">
                        <tr>
                            <td>Baris</td>
                            <td>
                                <input style="width: 40%" x-model="baris" type="text" class="form-control">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-8">
                    <table class="table" x-show="suntikan">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gram</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(d,i) in Array.from({length:baris})">
                                <tr>
                                    <td x-text="i+1"></td>
                                    <td>
                                        <input type="text" class="form-control" name="grade[]">
                                    </td>
                                    <td>
                                        <input value="SP" type="text" class="form-control" name="no_box[]">
                                    </td>
                                    <td>
                                        <select class="selectPgws" required x-init="initSelect2" name="pengawas[]"
                                            class="select2pgws" id="">
                                            <option value="">Pilih pengawas</option>
                                            @foreach ($pengawas as $p)
                                                <option value="{{ $p->id }}">{{ strtoupper($p->name) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-end" name="pcs_kredit[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-end" name="gr_kredit[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-end" name="rp_gram_kredit[]">
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>

                <div class="col-lg-2" x-show="suntikan">
                    <button class="mb-3 btn btn-sm btn-primary btn-block" type="submit">Simpan</button>
                </div>
            </form>
            <div class="col-lg-12">
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No Box</th>
                            <th>Grade</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gr</th>
                            <th class="text-end">Rp Gr</th>
                            <th class="text-end">Ttl Rp</th>
                            <th class="text-end">Pengawas</th>
                            <th class="text-end">Pcs Akhir</th>
                            <th class="text-end">Gr Akhir</th>
                            <th class="text-end">Cost Sortir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box_kecil as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->no_box }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ $g->pcs }}</td>
                                <td class="text-end">{{ $g->gr }}</td>
                                @php
                                    $ttlRp = $g->rp_gram * $g->gr;
                                @endphp
                                <td class="text-end">{{ number_format($g->rp_gram, 0) }}</td>
                                <td class="text-end">{{ number_format($ttlRp, 0) }}</td>
                                <td>{{ $g->name }}</td>
                                <td class="text-end">{{ number_format($g->pcs_sortir, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr_sortir, 0) }}</td>
                                <td class="text-end">{{ number_format($g->ttlrp_sortir, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>
        @section('js')
            <script>
                $('.select2pgws').select2()
            </script>
        @endsection
    </x-slot>
</x-theme.app>
