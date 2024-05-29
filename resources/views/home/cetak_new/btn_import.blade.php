<x-theme.button modal="Y" idModal="import" href="#" icon="fa-plus" addClass="float-end me-2" teks="box" />
<p class="badge bg-info text-wrap me-2">tekan CTRL + panah ⬅️kiri / kanan➡️ <br> untuk view hari kemarin
    & selanjutnya</p>
<form action="{{ route('cetaknew.import') }}" enctype="multipart/form-data" method="post">
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
                        <a href="{{ route('cetaknew.template') }}" class="btn btn-primary btn-sm"><i
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
                        <input type="hidden" name="kategori" value="{{ request()->get('kategori') ?? 'cabut' }}">
                    </td>
                </tr>
            </table>

        </div>
    </x-theme.modal>
</form>
