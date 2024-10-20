<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="mt-1">{{ $title }}</h6>
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
                @if (auth()->user()->posisi_id != 13)
                    <div>
                        @include('home.bk.btn_import')
                    </div>
                    <x-theme.button href="{{ route('bk.add', ['kategori' => $kategori]) }}" icon="fa-plus"
                        teks="Tambah" />
                    <a href="{{ route('bk.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2, 'kategori' => $kategori]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                    <x-theme.button href="#" icon="fa-print" addClass="btn_bayar" teks="Print" />
                    <div>
                        <x-theme.btn_filter />
                    </div>
                @endif
                @if (auth()->user()->posisi_id == 13)
                    <div>
                        <x-theme.btn_filter />
                    </div>
                @endif

            </div>
        </div>


    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @if ($kategori == 'nanda')
                <form action="{{ route('importperbaikan.index') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="col-lg-4">
                        <input type="file" name="file" class="form-control">
                        <button class="btn btn-primary" type="submit">Import Bk</button>
                    </div>
                </form>
                <form action="{{ route('importperbaikan.importperbaikansortir') }}" enctype="multipart/form-data"
                    method="post">
                    @csrf
                    <div class="col-lg-4">
                        <input type="file" name="file" class="form-control">
                        <button class="btn btn-primary" type="submit">Import Sortir</button>
                    </div>
                </form>
            @else
            @endif

            <div class="col-lg-8">
                {{-- @include('home.bk.nav', ['name' => 'index']) --}}
            </div>
            <div class="col-lg-4 mb-2">
                {{-- <table class="float-end">
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table> --}}
            </div>
            <div class="col-lg-12">
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
                            <th>Penerima</th>
                            <th>Pgws Grade</th>
                            <th class="text-end">Pcs Awal</th>
                            <th class="text-end">Gr Awal</th>
                            <th class="text-end">Susut</th>
                            <th>Status</th>
                            <th>
                                Cek
                                <center>
                                    {{-- @php
                                        $adaDitutup = DB::table('bk')
                                            ->where([['selesai', 'Y'], ['kategori', 'like', "%$kategori%"]])
                                            ->first();
                                    @endphp
                                    @if (!empty($adaDitutup))
                                    @endif --}}
                                    <input style="text-align: center" type="checkbox" class="form-check"
                                        id="cekSemuaTutup">
                                    <br>
                                    <span class="badge bg-danger btn_tutup d-none" tipe="tutup"
                                        style="cursor: pointer"><i class="fas fa-check"></i> Tutup </span>

                                    {{-- <x-theme.button href="#" icon="fa-check" variant="danger" addClass="btn_tutup"
                                    teks="Tutup" /> --}}
                                </center>
                            </th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bk as $no => $b)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $b->nm_partai }} </td>
                                <td>{{ $b->no_box }}</td>
                                <td>{{ $b->tipe }}</td>
                                <td>{{ $b->ket }}</td>
                                <td>{{ $b->warna }}</td>
                                <td>{{ tanggal($b->tgl) }}</td>
                                <td>{{ $b->pengawas }}</td>
                                <td>{{ $b->name }}</td>
                                <td>{{ $b->pgws_grade }}</td>
                                <td class="text-end">{{ $b->pcs_awal }}</td>
                                <td class="text-end">{{ $b->gr_awal }}</td>
                                <td class="text-end">{{ $b->susut }}</td>
                                <td align="center">
                                    @if ($b->selesai == 'T')
                                        <span class="badge bg-warning">BELUM</span>
                                    @else
                                        <span class="badge bg-primary">SELESAI</span>
                                    @endif
                                </td>
                                <td align="center">
                                    @if ($b->selesai == 'T')
                                        <input type="checkbox" penerima="{{ $b->penerima }}"
                                            no_nota="{{ $b->id_bk }}" class="cek_bayar" name=""
                                            id="">
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $idUser = session()->get('id_user');
                                        $waktu = session()->get('waktu');
                                        $cek = date('Y-m-d') == $b->tgl_input;
                                    @endphp
                                    @if ($cek || Auth::user()->posisi_id == 1)
                                        <span style="cursor: pointer" class="badge bg-primary selesai"><i
                                                class="fas fa-check"></i></span>
                                        <span style="cursor: pointer" class="badge bg-warning edit_bk"><i
                                                class="fas fa-edit"></i></span>
                                        <span style="cursor: pointer" class="badge bg-danger delete"><i
                                                class="fas fa-trash-alt"></i></span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </section>
        <x-theme.modal idModal="loading" btnSave="T" disabled="true" title="Tunggu loading">
            mohon tunggu loading...
            <br>
            <div class="row justify-content-center">
                <img src="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20width='38'%20height='38'%20stroke='%235d79d3'%20viewBox='0%200%2038%2038'%3e%3cg%20fill='none'%20fill-rule='evenodd'%3e%3cg%20stroke-width='2'%20transform='translate(1%201)'%3e%3ccircle%20cx='18'%20cy='18'%20r='18'%20stroke-opacity='.5'/%3e%3cpath%20d='M36%2018c0-9.94-8.06-18-18-18'%3e%3canimateTransform%20attributeName='transform'%20dur='1s'%20from='0%2018%2018'%20repeatCount='indefinite'%20to='360%2018%2018'%20type='rotate'/%3e%3c/path%3e%3c/g%3e%3c/g%3e%3c/svg%3e"
                    class="me-4" style="width: 100px" alt="audio">
            </div>
        </x-theme.modal>



    </x-slot>
    @section('scripts')
        <script>
            $(document).ready(function() {

                inputChecked('cekSemuaTutup', 'cek_bayar')
                pencarian('pencarian', 'tblPenc')

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
                        var targetUrl = `/home/bk/${link}?kategori=${kategori}&${queryString}`

                        if (formDelete === null) {
                            window.location.assign(targetUrl)
                        } else {
                            if (confirm(formDelete)) {
                                $.ajax({
                                    type: "POST",
                                    url: `/home/bk/${link}`,
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
                clickCekKirim('.btn_bayar', 'print')
                clickCekKirim('.edit_bk', 'edit')
                clickCekKirim('.delete', 'delete', 'Yakin ingin dihapus ?')
                clickCekKirim('.selesai', 'selesai', 'Yakin ingin diselesaikan ?')

                $(".btn_bayar").hide();
                $(".piutang_cek").hide();
                $(".delete").hide();
                $(".edit_bk").hide();
                $(".selesai").hide();

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
                    $(".selesai").toggle(anyChecked);
                    $('.piutangBayar').text(totalPiutang.toLocaleString('en-US'));
                });

            });
        </script>
    @endsection
</x-theme.app>
