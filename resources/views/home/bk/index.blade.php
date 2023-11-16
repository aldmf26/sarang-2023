<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>
        <x-theme.button href="#" icon="fa-print" addClass="float-end btn_bayar" teks="Print" />
        <a href="{{ route('bk.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm icon icon-left btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>

        <x-theme.button href="{{ route('bk.add', ['kategori' => $kategori]) }}" icon="fa-plus" addClass="float-end"
            teks="Tambah" />
            @if (auth()->user()->name == 'aldi')
            <x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end"
                teks="Import" />
            @endif
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8">
                @include('home.bk.nav', ['name' => 'index'])
            </div>
            <div class="col-lg-4 mb-2">
                <table class="float-end">
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table>
            </div>
            <table class="table" id="tablealdi">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Lot</th>
                        <th>No Box</th>
                        <th>Tipe</th>
                        <th>Ket</th>
                        <th>Warna</th>
                        <th>Tgl terima</th>
                        <th>Pengawas</th>
                        <th>Penerima</th>
                        <th class="text-end">Pcs Awal</th>
                        <th class="text-end">Gr Awal</th>
                        <th>Cek</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bk as $no => $b)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $b->no_lot }}</td>
                            <td>{{ $b->no_box }}</td>
                            <td>{{ $b->tipe }}</td>
                            <td>{{ $b->ket }}</td>
                            <td>{{ $b->warna }}</td>
                            <td>{{ tanggal($b->tgl) }}</td>
                            <td>{{ $b->pengawas }}</td>
                            <td>{{ $b->name }}
                            </td>
                            <td class="text-end">{{ $b->pcs_awal }}</td>
                            <td class="text-end">{{ $b->gr_awal }}</td>
                            <td align="center"><input type="checkbox" no_nota="{{ $b->no_box }}" class="cek_bayar"
                                    name="" id=""></td>
                            <td>
                                <a href="" class="btn btn-sm btn-warning edit_bk"><i class="fas fa-edit"></i></a>
                                <a href="" class="btn btn-sm btn-danger delete"><i
                                        class="fas fa-trash-alt"></i></a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </section>
        <form action="{{ route('bk.import') }}" enctype="multipart/form-data" method="post">
            @csrf
        <x-theme.modal size="modal-lg" idModal="import" title="Import Bk">
            <div class="row">
                <table>
                    <tr>
                        <td width="100" class="pl-2">
                            <img width="80px" src="{{ asset('/img/1.png') }}" alt="">
                        </td>
                        <td>
                            <span style="font-size: 20px;"><b> Download Excel template</b></span><br>
                            File ini memiliki kolom header dan isi yang sesuai dengan data menu
                        </td>
                        <td>
                            <a href="{{ route('bk.template') }}" class="btn btn-primary btn-sm"><i
                                    class="fa fa-download"></i> DOWNLOAD
                                TEMPLATE</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="pl-2">
                            <img width="80px" src="{{ asset('/img/2.png') }}" alt="">
                        </td>
                        <td>
                            <span style="font-size: 20px;"><b> Upload Excel template</b></span><br>
                            Setelah mengubah, silahkan upload file.
                        </td>
                        <td>
                            <input type="file" name="file" class="form-control">
                        </td>
                    </tr>
                </table>

            </div>
        </x-theme.modal>
     

    </x-slot>
    @section('scripts')
        <script>
            $(document).ready(function() {

                pencarian('pencarian', 'tablealdi')
                // $(document).on("click", ".detail_nota", function() {
                //     var no_nota = $(this).attr('no_nota');
                //     $.ajax({
                //         type: "get",
                //         url: "/detail_invoice_telur?no_nota=" + no_nota,
                //         success: function(data) {
                //             $("#detail_invoice").html(data);
                //         }
                //     });

                // });
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
                        var targetUrl = `/home/bk/${link}?` + queryString
                        if (formDelete === null) {
                            window.location.assign(targetUrl)
                        } else {
                            if (confirm('Yakin ingin dihapus ? ')) {
                                window.location.assign(targetUrl)
                                // window.open(targetUrl);
                            }

                        }

                    });
                }
                clickCekKirim('.btn_bayar', 'print')
                clickCekKirim('.edit_bk', 'edit')
                clickCekKirim('.delete', 'delete', 'formDelete')

                $(".btn_bayar").hide();
                $(".piutang_cek").hide();
                $(".delete").hide();
                $(".edit_bk").hide();

                $(document).on('change', '.cek_bayar', function() {
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
                    $('.piutangBayar').text(totalPiutang.toLocaleString('en-US'));
                });

            });
        </script>
    @endsection
</x-theme.app>
