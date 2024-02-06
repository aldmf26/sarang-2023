<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>
        <x-theme.button href="{{ route('pengiriman.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end"
            teks="Import" />
        <form action="{{ route('pengiriman.import') }}" enctype="multipart/form-data" method="post">
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
                                <a href="{{ route('pengiriman.template') }}" class="btn btn-primary btn-sm"><i
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
        </form>
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">


        <section class="row">
            <div class="col-lg-4 mb-2 ">
                <table>
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table>
            </div>
            <div class="col-lg-12">
                <table class="table table-stripped" id="tablealdi">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Partai</th>
                            <th>Grade</th>
                            <th>Tipe</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                            <th>Pcs Akhir</th>
                            <th>Gr Akhir</th>
                            <th>No Box Cfm</th>
                            <th>Cek Akhir</th>
                            <th>Ket</th>
                            <th>
                                Cek
                                <center>
                                    <input style="text-align: center" type="checkbox" class="form-check"
                                        id="cekSemuaTutup">
                                    <br>
                                    <span class="badge bg-danger btn_tutup d-none" tipe="tutup"
                                        style="cursor: pointer"><i class="fas fa-check"></i> Tutup </span>
                                </center>
                            </th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengiriman as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $d->partai }}</td>
                                <td>{{ $d->grade }}</td>
                                <td>{{ $d->tipe }}</td>
                                <td>{{ number_format($d->pcs, 0) }}</td>
                                <td>{{ number_format($d->gr, 0) }}</td>
                                <td>{{ number_format($d->pcs_akhir, 0) }}</td>
                                <td>{{ number_format($d->gr_akhir, 0) }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->cek_akhir }}</td>
                                <td>{{ $d->ket }}</td>
                                <td align="center">
                                    <input type="checkbox" penerima="{{ $d->admin }}"
                                        no_nota="{{ $d->id_pengiriman }}" class="cek_bayar" name=""
                                        id="">
                                </td>
                                <td align="center">

                                    <span style="cursor: pointer" class="badge bg-warning edit_bk"><i
                                            class="fas fa-edit"></i></span>
                                    <span style="cursor: pointer" class="badge bg-danger delete"><i
                                            class="fas fa-trash-alt"></i></span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </section>

    </x-slot>
    @section('scripts')
        <script>
            inputChecked('cekSemuaTutup', 'cek_bayar')
            pencarian('pencarian', 'tablealdi')

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
                    };
                    var targetUrl = `/home/pengiriman/${link}?${queryString}`

                    if (formDelete === null) {
                        window.location.assign(targetUrl)
                    } else {
                        if (confirm(formDelete)) {
                            $.ajax({
                                type: "POST",
                                url: `/home/pengiriman/${link}`,
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
        </script>
    @endsection
</x-theme.app>
