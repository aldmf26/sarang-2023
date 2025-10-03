<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}
                    {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}
                    <span class="text-warning" style="font-size: 12px"><em>jika data tidak ada silahkan view dulu
                            !</em></span>
                </h6>
            </div>

            <div class="col-lg-6">
                @include('home.cabut.btn_export_global')
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.cetak.nav')

        </div>

    </x-slot>

    <x-slot name="cardBody">
        <form action="" method="GET">
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label for="divisi">Divisi</label>
                        <select required name="divisi" id="divisi" class="form-select">
                            <option value="">Pilih divisi</option>
                            <option @selected($divisi == 'cetak') value="cetak">cabut</option>
                            <option @selected($divisi == 'eo') value="eo">eo</option>
                            <option @selected($divisi == 'sortir') value="sortir">cetak</option>
                            <option @selected($divisi == 'grade') value="grade">sortir</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="bulan">Bulan</label>
                        <select name="bulan" id="bulan" class="select2bulan">
                            @php
                                $listBulan = DB::table('bulan')->get();
                            @endphp
                            @foreach ($listBulan as $l)
                                <option value="{{ $l->bulan }}" {{ $bulan == $l->bulan ? 'selected' : '' }}>
                                    {{ $l->nm_bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <label for="">Aksi</label>
                    <br>
                    <button type="submit" class="btn btn-sm btn-primary">View</button>
                </div>

            </div>
        </form>

        <div class="row">
            <div class="col-12">
                <h6>Susut Perbulan Mei</h6>
                <form action="" method="post">
                    @csrf
                    <table class=" table table-bordered table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Gr Awal</th>
                                <th>Gr Akhir</th>
                                <th>Sst %</th>
                                <th>Sst Program</th>
                                <th>Rbg 1</th>
                                <th>Rbg 2</th>
                                <th>Rbg 3</th>
                                <th>Sapuan Lantai</th>
                                <th>Sesetan</th>
                                <th>Bulu</th>
                                <th>Pasir</th>
                                <th>Rtk Bk</th>
                                <th>Flx</th>
                                <th>Ttl Aktual</th>
                                <th>Selisih</th>
                                <th>Sst %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($susutData as $p => $pengawas)
                                @php
                                    $susutSudahAda = DB::table('tb_susut')
                                        ->where('id_pemberi', $pengawas->id_pengawas)
                                        ->where('bulan_dibayar', $bulan)
                                        ->where('divisi', $divisi)
                                        ->first();
                                @endphp

                                <tr>
                                    <td>{{ $p }}</td>
                                    <td align="right">{{ number_format($pengawas->gr_awal, 0) }}</td>
                                    <td align="right">{{ number_format($pengawas->gr_akhir, 0) }}</td>

                                    @php
                                        $susutPersen = empty($pengawas->gr_awal)
                                            ? 0
                                            : (1 - $pengawas->gr_akhir / $pengawas->gr_awal) * 100;
                                    @endphp
                                    <td align="right">{{ number_format($susutPersen, 0) }}%</td>
                                    <td align="right">{{ number_format($pengawas->sst_program, 0) }}</td>
                                    <td>
                                        <input type="hidden" name="id_pemberi[]" value="{{ $pengawas->id_pengawas }}">
                                        <input type="text" class="form-control form-control-sm" name="rambangan_1[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->rambangan_1 : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="rambangan_2[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->rambangan_2 : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="rambangan_3[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->rambangan_3 : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            name="sapuan_lantai[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->sapuan_lantai : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="sesetan[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->sesetan : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="bulu[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->bulu : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="pasir[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->pasir : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="rontokan_bk[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->rontokan_bk : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="flx[]"
                                            value="{{ $susutSudahAda ? $susutSudahAda->flx : '' }}">
                                    </td>
                                    @php
                                        $ttlAktual = $susutSudahAda ? $susutSudahAda->ttl_aktual : 0;

                                        if (
                                            empty($pengawas->gr_awal) ||
                                            empty($pengawas->sst_program) ||
                                            $pengawas->sst_program == 0
                                        ) {
                                            $susutPersenSelisih = 0;
                                        } else {
                                            $susutPersenSelisih = (1 - $ttlAktual / $pengawas->sst_program) * 100;
                                        }
                                    @endphp

                                    <td align="right">{{ number_format($ttlAktual, 0) }}</td>

                                    <td align="right">{{ number_format($pengawas->sst_program - $ttlAktual, 0) }}</td>
                                    <td align="right">{{ number_format($susutPersenSelisih, 0) }}%</td>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class=" float-end btn btn-sm btn-primary">Simpan</button>
                </form>
            </div>

        </div>
    </x-slot>

    @section('scripts')
        <script>
            $('.select2bulan').select2({})
        </script>
    @endsection
</x-theme.app>
