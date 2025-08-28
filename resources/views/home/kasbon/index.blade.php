<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <a class="float-end btn btn-sm icon icon-left btn-primary me-2"
            href="{{ route('denda.print', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"><i class="fas fa-print"></i> Print</a>
    </x-slot>
    @php
        $rot = request()->route()->getName();
    @endphp
    <x-slot name="cardBody">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'denda.index' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('denda.index') }}">Denda</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'kasbon.index' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('kasbon.index') }}">Kasbon</a>
            </li>

        </ul>
        <br>
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th class="text-end">Nominal (Rp. {{ number_format($ttlNominal, 0) }})</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kasbon as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($d->tgl) }}</td>
                            <td>{{ ucwords($d->nama) }}</td>
                            <td align="right">{{ number_format($d->nominal, 0) }}</td>

                            <td>
                                <x-theme.button modal="Y" idModal="delete" href="#" icon="fa-trash"
                                    variant="danger" addClass="float-end delete_nota" teks=""
                                    data="no_nota={{ $d->id }}" />

                                <x-theme.button modal="Y" idModal="edit" href="#" icon="fa-pen"
                                    addClass="float-end edit" teks="" data="id={{ $d->id }}" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- ALL MODAL --}}
        <form action="{{ route('kasbon.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah kasbon" btnSave="Y">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Nama Anak</label>
                            <select required name="id_anak[]" multiple class="select2" id="">
                                @foreach ($anak as $k)
                                    <option value="{{ $k->id_anak }}">{{ strtoupper($k->nama) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Bulan dibayar</label>
                        <select name="bulan_dibayar" class="form-control select2" id="">
                            <option value="">- Pilih Bulan -</option>

                            @foreach (getListBulan() as $b)
                                <option value="{{ $b->bulan }}">{{ strtoupper($b->nm_bulan) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Tanggal</label>
                            <input type="date" name="tgl" value="{{ date('Y-m-d') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Nominal</label>
                            <input required type="number" min="0" name="nominal" class="form-control">
                        </div>
                    </div>


                </div>
            </x-theme.modal>
        </form>

        {{-- update --}}
        <form action="{{ route('kasbon.update') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Kasbon" idModal="edit">
                <div id="get_edit">
                </div>
            </x-theme.modal>
        </form>

        {{-- delete --}}
        <x-theme.btn_alert_delete route="kasbon.delete" name="id_denda" tgl1="2022" tgl2="2022" id_proyek="1" />

    </x-slot>

    @section('js')
        <script>
            $(document).ready(function() {
                $(".select3").select2()
                $(document).on('click', '.edit', function() {
                    var id = $(this).attr('id')
                    $.ajax({
                        type: "GET",
                        url: `{{ route('kasbon.detail') }}?id=` + id,
                        success: function(r) {

                            $("#get_edit").html(r);
                        }
                    });
                })
            });
        </script>
    @endsection
</x-theme.app>
