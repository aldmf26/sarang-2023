<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')

    </x-slot>

    <x-slot name="cardBody">


        <section class="row" x-data="{
            cek: [],
            async redir(path) {
                const deleteToken = '{{ session('delete_token') }}';
                window.location.href = `/home/gradingbj/boxkirim/${path}?id_pengiriman=${this.cek}&token=${deleteToken}`;
            },
            edit() {
                this.redir('edit')
            },
            del() {
                if (confirm('Apakah anda yakin ?')) {
                    this.redir('delete')
                }
            },
        }">
            <div class="d-flex justify-content-between">
                <div>
                    <table>
                        <td>Pencarian :</td>
                        <td><input type="text" id="pencarian" class="form-control float-end"></td>
                    </table>
                </div>
                <div>
                    <span x-show="cek.length" class="btn btn-sm btn-info" x-transition>Data dipilih <span
                            class="badge bg-primary" x-text="cek.length" x-transition></span></span>
                    {{-- <x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end"
                        teks="Import" />
                    <form action="{{ route('pengiriman.import') }}" enctype="multipart/form-data" method="post">
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
                                            <a href="{{ route('pengiriman.template') }}"
                                                class="btn btn-primary btn-sm"><i class="fa fa-download"></i> DOWNLOAD
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
                    </form> --}}
                </div>
            </div>
            <x-theme.alert pesan="{{ session()->get('error') }}" />
            <div class="col-lg-12 mt-2">
                <div class="scrollable-table">
                    <table class="table table-stripped" id="tblBox">
                        <thead>
                            <tr>
                                <th class="dhead">No Grading</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                {{-- <th class="dhead">
                                    Cek
                                    <center>
                                    <input style="text-align: center" type="checkbox" class="form-check"
                                        id="cekSemuaTutup">
                                    <br>
                                    <span class="badge bg-danger btn_tutup d-none" tipe="tutup"
                                        style="cursor: pointer"><i class="fas fa-check"></i> Tutup </span>
                                </center>
                                </th> --}}
                                <th width="80" class="text-center dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($boxKirim as $d)
                                <tr>
                                    <td>SP{{ $d->no_box }} </td>
                                    <td>{{ $d->grade }}</td>
                                    <td>D</td>
                                    <td class="text-end">{{ $d->pcs }}</td>
                                    <td class="text-end">{{ $d->gr }}</td>
                                    <td align="center" class="d-flex justify-content-center gap-2">
                                        <div>
                                            <input x-model="cek" value="{{ $d->id_pengiriman }}" type="checkbox"
                                                class="form-check">
                                        </div>
                                        <span x-show="cek.length" @click="edit()" style="cursor: pointer"
                                            class="badge bg-warning"><i class="fas fa-edit"></i></span>
                                        <span x-show="cek.length" @click="del()" style="cursor: pointer"
                                            class="badge bg-danger"><i class="fas fa-trash-alt"></i></span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </section>

    </x-slot>
    @section('scripts')
        <script>
            pencarian('pencarian', 'tblBox')
        </script>
    @endsection
</x-theme.app>
