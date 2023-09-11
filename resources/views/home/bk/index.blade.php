<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="#" icon="fa-print" addClass="float-end btn_bayar" teks="Print" />
        <a href="{{ route('bk.export',['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}" class="btn btn-sm icon icon-left btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>

        <x-theme.button href="#" icon="fa-file-excel" addClass="float-end btn_excel" teks="Export" />
        <x-theme.button href="{{ route('bk.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <x-theme.btn_filter />

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8"></div>
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
                        <th>Pcs Awal</th>
                        <th>Gr Awal</th>
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
                            <td>{{ $b->ket_bk }}</td>
                            <td>{{ $b->nm_warna }}</td>
                            <td>{{ tanggal($b->tgl) }}</td>
                            <td>{{ $b->pengawas }}</td>
                            <td>{{ $b->penerima == '1' ? 'Jenah' : ($b->penerima == '2' ? 'Nurul' : 'Erna') }}
                            </td>
                            <td>{{ $b->pcs_awal }}</td>
                            <td>{{ $b->gr_awal }}</td>
                            <td align="center"><input type="checkbox" no_nota="{{ $b->no_box }}" class="cek_bayar"
                                    name="" id=""></td>
                            <td>
                                @if (date('Y-m-d') == $b->tgl)
                                    <a href="" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </section>

    </x-slot>
    @section('scripts')
        <script>
            $(document).ready(function() {
                $('.btn_excel').click(function(e) {
                    e.preventDefault();
                    window.location.href = "bk/export?tgl1={{ $tgl1 }}&tgl2={{ $tgl2 }}"
                });
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

                $(document).on('click', '.btn_bayar', function() {
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
                    // window.location.href = "/print?" + queryString;
                    window.open("/home/bk/print?" + queryString, '_blank');

                });
                $(".btn_bayar").hide();
                $(".piutang_cek").hide();
                $(document).on('change', '.cek_bayar', function() {
                    var totalPiutang = 0
                    $('.cek_bayar:checked').each(function() {
                        var piutang = $(this).attr('piutang');
                        totalPiutang += parseInt(piutang);
                    });
                    var anyChecked = $('.cek_bayar:checked').length > 0;
                    $('.btn_bayar').toggle(anyChecked);
                    $(".piutang_cek").toggle(anyChecked);
                    $('.piutangBayar').text(totalPiutang.toLocaleString('en-US'));
                });

                $('.hide_bayar').hide();
                $(document).on("click", ".detail_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    var clickedElement = $(this); // Simpan elemen yang diklik dalam variabel

                    clickedElement.prop('disabled', true); // Menonaktifkan elemen yang diklik

                    $.ajax({
                        type: "get",
                        url: "/get_pembayaranpiutang_telur?no_nota=" + no_nota,
                        success: function(data) {
                            $('.induk_detail' + no_nota).after("<tr>" + data + "</tr>");
                            $(".show_detail" + no_nota).show();
                            $(".detail_bayar" + no_nota).hide();
                            $(".hide_bayar" + no_nota).show();

                            clickedElement.prop('disabled',
                                false
                            ); // Mengaktifkan kembali elemen yang diklik setelah tampilan ditambahkan
                        },
                        error: function() {
                            clickedElement.prop('disabled',
                                false
                            ); // Jika ada kesalahan dalam permintaan AJAX, pastikan elemen yang diklik diaktifkan kembali
                        }
                    });
                });
                $(document).on("click", ".hide_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    $(".show_detail" + no_nota).remove();
                    $(".detail_bayar" + no_nota).show();
                    $(".hide_bayar" + no_nota).hide();

                });
            });
        </script>
    @endsection
</x-theme.app>
