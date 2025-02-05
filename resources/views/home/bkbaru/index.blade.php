<x-theme.app title="{{ $title }}" table="T" sizeCard="12">
    <x-slot name="slot">
        <section class="row">
            <div x-data="{
                cek: [],
                selectedItem: [],
                tambah(no_box, nm_partai, pcs, gr) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
            
                    const index = selectedItem.findIndex(item => item.no_box === no_box);
                    if (index === -1) {
                        selectedItem.push({
                            no_box: no_box,
                            nm_partai: nm_partai,
                            pcs: parseFloat(pcs),
                            gr: parseFloat(gr),
                        });
                    } else {
                        selectedItem.splice(index, 1);
                    }
            
                },
                formatNumber(value) {
                    // Format number with '.' as thousands separator and ',' as decimal separator
                    return new Intl.NumberFormat('id-ID', { style: 'decimal', maximumFractionDigits: 0 }).format(value);
                }
            }">
                <div class="d-flex justify-content-between">
                    <h6 class="mt-1">{{ $title }} : {{ $bk_terakhir }}</h6>
                    <div class="d-flex gap-1">
                        {{-- <div class="{{ auth()->user()->posisi_id != 13 ? '' : 'd-none' }}"> --}}
                        {{-- <div class="">
                        <a href="{{ route('bk.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2, 'kategori' => $kategori]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-file-excel"></i> Export
                        </a>
                        <x-theme.button href="{{ route('bk.add', ['kategori' => $kategori]) }}" icon="fa-plus"
                            teks="Tambah" />
                        <div>
                            @include('home.bk.btn_import')
                        </div>
                    </div> --}}
                        {{-- <div>
                        @include('home.bk.btn_import')
                    </div> --}}
                        @if (auth()->user()->posisi_id == 13)
                            <div>
                                <x-theme.btn_filter />
                            </div>
                        @else
                            <div>
                                @include('home.bk.btn_import')
                            </div>
                            <x-theme.button href="{{ route('bkbaru.add') }}" icon="fa-plus" teks="Tambah Data" />

                            <a href="{{ route('bk.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2, 'kategori' => 'cabut']) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-file-excel"></i> Export
                            </a>
                            <x-theme.button href="" addClass="serah" icon="fa-plus" variant="info" modal="Y"
                                idModal="tambah" teks="Serah" />
                            <button class="btn btn-sm btn-warning edit_bk"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-danger delete"><i class="fas fa-trash-alt"></i> Hapus</button>
                        @endif
                        <x-theme.button href="{{ route('bkbaru.invoice') }}" icon="fa-clipboard-list" teks="Po Cabut" />




                    </div>
                </div>


                <div class="col-lg-8">
                    {{-- @include('home.bk.nav', ['name' => 'index']) --}}
                </div>

                <div class="col-lg-12">
                    <br>
                    <br>
                    <table class="table" id="tablealdi">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Partai</th>
                                <th>No Box</th>
                                <th>Tipe</th>
                                <th>Ket</th>
                                <th>Warna</th>
                                <th>Tgl terima</th>
                                <th>Pengawas</th>
                                <th>Pgws Grade</th>
                                <th class="text-end">Pcs Awal</th>
                                <th class="text-end">Gr Awal</th>
                                @if (auth()->user()->posisi_id == 13)
                                @else
                                    <th class="text-center">Cek <br>
                                        <span class="badge bg-primary" x-show="cek.length" x-text="cek.length"></span>
                                    </th>
                                @endif

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bk as $no => $a)
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    <td>{{ $a->nm_partai }}</td>
                                    <td>{{ $a->no_box }}</td>
                                    <td>{{ $a->tipe }}</td>
                                    <td>{{ $a->ket }}</td>
                                    <td>{{ $a->warna }}</td>
                                    <td>{{ tanggal($a->tgl) }}</td>
                                    <td>{{ $a->pengawas }}</td>
                                    <td>{{ $a->pgws_grade }}</td>
                                    <td>{{ $a->pcs_awal }}</td>
                                    <td>{{ $a->gr_awal }}</td>
                                    @if (auth()->user()->posisi_id == 13)
                                    @else
                                        <td class="text-center">
                                            <input type="checkbox" class="cek_bayar" no_nota="{{ $a->id_bk }}"
                                                @change="tambah({{ $a->no_box }}, '{{ $a->nm_partai }}', {{ $a->pcs_awal }}, {{ $a->gr_awal }})"
                                                value="{{ $a->no_box }}" x-model="cek">
                                        </td>
                                    @endif


                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>

                <form action="{{ route('bkbaru.save_formulir') }}" method="post">
                    @csrf
                    <x-theme.modal idModal="tambah" title="tambah box" btnSave="Y">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Tgl</label>
                                    <input value="{{ date('Y-m-d') }}" type="date" name="tgl"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="">Pgws Penerima</label>
                                <select required name="id_penerima" class="form-control select2" id="">
                                    <option value="">- Pilih pgws -</option>
                                    @foreach ($users as $d)
                                        <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="dhead">Partai</th>
                                            <th class="dhead">No Box</th>
                                            <th class="dhead text-end">Pcs</th>
                                            <th class="dhead text-end">Gr</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <input class="d-none" name="no_box[]" type="text" :value="cek">
                                        <tr>
                                            <td style="background-color: #f3a36e; color:white">Total</td>
                                            <td style="background-color: #f3a36e; color:white"
                                                x-text="selectedItem.length + ' Box'"></td>
                                            <th style="background-color: #f3a36e; color:white" class="text-end"
                                                x-text="selectedItem.reduce((acc, cur) => acc + cur.pcs, 0)"></th>
                                            <th style="background-color: #f3a36e; color:white" class="text-end"
                                                x-text="selectedItem.reduce((acc, cur) => acc + cur.gr, 0)"></th>
                                        </tr>
                                        <template x-for="item in selectedItem">
                                            <tr>

                                                <td x-text="item.nm_partai"></td>
                                                <td x-text="item.no_box"></td>
                                                <td align="right" x-text="item.pcs"></td>
                                                <td align="right" x-text="item.gr"></td>

                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </x-theme.modal>
                </form>
            </div>
        </section>




    </x-slot>
    @section('scripts')
        <script>
            $(".edit_bk").hide();
            $(".delete").hide();
            $(".serah").hide();

            $(document).on('change', '.cek_bayar, #cekSemuaTutup', function() {
                var totalPiutang = 0
                $('.cek_bayar:checked').each(function() {
                    var piutang = $(this).attr('piutang');
                    totalPiutang += parseInt(piutang);
                });
                var anyChecked = $('.cek_bayar:checked').length > 0;
                $('.btn_bayar').toggle(anyChecked);
                $(".piutang_cek").toggle(anyChecked);
                $('.delete').toggle(anyChecked);
                $(".edit_bk").toggle(anyChecked);
                $(".serah").toggle(anyChecked);
                $('.piutangBayar').text(totalPiutang.toLocaleString('en-US'));
            });

            function clickCekKirim(kelas, link, formDelete = null) {
                $(document).on('click', `${kelas}`, function(e) {
                    e.preventDefault();

                    var dipilih = [];
                    $('.cek_bayar:checked').each(function() {
                        var no_nota = $(this).attr('no_nota');
                        dipilih.push(no_nota);
                    });
                    var params = new URLSearchParams();
                    dipilih.forEach(function(orderNumber) {
                        params.append('no_nota', orderNumber);
                    });
                    var queryString = 'no_nota[]=' + dipilih.join('&no_nota[]=');

                    var kategori = "{{ request()->get('kategori') ?? 'cabut' }}"
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var postData = {
                        _token: csrfToken,
                        no_nota: dipilih,
                        kategori: kategori,
                    };
                    var targetUrl = `/home/bkbaru/${link}?kategori=${kategori}&${queryString}`

                    if (formDelete === null) {
                        window.location.assign(targetUrl)
                    } else {
                        if (confirm(formDelete)) {
                            $.ajax({
                                type: "POST",
                                url: `/home/bkbaru/${link}`,
                                data: postData,
                                beforeSend: function() {
                                    $("#loading").modal('show')
                                },
                                success: function(r) {
                                    window.location.reload()
                                },
                                error: function(error) {
                                    // Handle error if needed
                                    console.error(error);
                                }
                            });
                        }
                    }
                });
            }
            clickCekKirim('.edit_bk', 'edit')
            clickCekKirim('.delete', 'delete', 'Yakin ingin dihapus ?')
        </script>
    @endsection
</x-theme.app>
