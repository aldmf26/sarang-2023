<x-theme.app>
    <x-slot name="cardHeader">
        <h6 class="float-start">{{ $title }}</h6>
        <x-theme.button href="#" idModal="gudang" modal="Y" icon="fa-warehouse" addClass="float-end"
            teks="Gudang Sudah Grade" />
        <x-theme.button href="#" icon="fa-plus" addClass="float-end ambil_box_kecil" teks="Ambil Box Kecil" />
        <x-theme.button href="{{ route('gradingbj.add') }}" icon="fa-plus" addClass="float-end" teks="Ambil dari ctk" />
        <x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end"
            teks="Import" />
        <form action="{{ route('gradingbj.import') }}" enctype="multipart/form-data" method="post">
            @csrf
            <x-theme.modal size="modal-lg" idModal="import" title="Import Pengiriman">
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
                                <a href="{{ route('gradingbj.template') }}" class="btn btn-primary btn-sm"><i
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
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8 mb-3">
                @include('home.packing.nav')
            </div>
       
        </section>
    </x-slot>
</x-theme.app>
