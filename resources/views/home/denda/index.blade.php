<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <a class="float-end btn btn-sm icon icon-left btn-primary me-2" href="{{ route('denda.print', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"><i class="fas fa-print"></i> Print</a>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Bulan dibayar</th>
                        <th>Tgl</th>
                        <th>Nama</th>
                        <th class="text-end">Nominal (Rp. {{number_format($ttlNominal,0)}})</th>
                        <th>Ket</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($denda as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ !empty($d->bulan_dibayar) ? date('M y', strtotime('01-' . $d->bulan_dibayar . '-' . date('Y'))) : '' }}
                            <td>{{ tanggal($d->tgl) }}</td>
                            <td>{{ ucwords($d->nama) }}</td>
                            <td align="right">{{ number_format($d->nominal,0) }}</td>
                            <td>{{ ucwords($d->ket) }}</td>
                            <td>
                                <x-theme.button modal="Y" idModal="delete" href="#" icon="fa-trash" variant="danger" addClass="float-end delete_nota" teks=""
                                    data="no_nota={{ $d->id_denda }}" />

                                <x-theme.button modal="Y" idModal="edit" href="#" icon="fa-pen" addClass="float-end edit" teks=""
                                    data="id={{ $d->id_denda }}" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- ALL MODAL --}}
        <form action="{{ route('denda.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah user" btnSave="Y">
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
                            @php
                                $bulan = DB::table('bulan')->get();
                            @endphp
                            @foreach ($bulan as $b)
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
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <input type="text" name="ket" class="form-control">
                        </div>
                    </div>

                </div>
            </x-theme.modal>
        </form>

        {{-- update --}}
        <form action="{{ route('denda.update') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Denda" idModal="edit">
                <div id="get_edit">
                </div>
            </x-theme.modal>
        </form>

        {{-- delete --}}
        <x-theme.btn_alert_delete route="denda.delete" name="id_denda" tgl1="2022" tgl2="2022" id_proyek="1" />

    </x-slot>

    @section('js')
        <script>
            $(document).ready(function() {
                $(".select3").select2()
                $(document).on('click', '.edit', function(){
                    var id = $(this).attr('id')
                    $.ajax({
                        type: "GET",
                        url: `{{route('denda.detail')}}?id=`+id,
                        success: function (r) {

                            $("#get_edit").html(r);
                        }
                    });
                })
            });
        </script>
    @endsection
</x-theme.app>
