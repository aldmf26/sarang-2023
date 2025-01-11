<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                class="fas fa-plus"></i>Data</a>
        <a href="{{ route('hrga8_1.print') }}" target="_blank" class="btn btn-primary float-end me-2"><i
                class="fas fa-print"></i> Print</a>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th class="text-center dhead" rowspan="2">No</th>
                            <th class="text-center dhead" rowspan="2">Nama Mesin</th>
                            <th class="text-center dhead" rowspan="2">Merek</th>
                            <th class="text-center dhead" rowspan="2">No. Mesin</th>
                            <th class="text-center dhead" rowspan="2">Lokasi</th>
                            <th class="text-center dhead" rowspan="2">Frekuensi Perawatan</th>
                            <th class="text-center dhead" rowspan="2">Penanggung Jawab</th>
                            <th class="text-center dhead" colspan="12">Tahun {{ $tahun }}</th>
                        </tr>
                        <tr>
                            @foreach ($bulan as $b)
                                <th class="text-center dhead">{{ $b->bulan }}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($pemeliharaan as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama }}</td>
                                <td class="text-center">{{ $p->merk }}</td>
                                <td class="text-center">{{ $p->no_identifikasi }}</td>
                                <td class="text-center">{{ $p->lokasi }}</td>
                                <td class="text-center">Setiap {{ $p->frekuensi_perawatan }} bulan</td>
                                <td class="text-center"> {{ $p->penanggung_jawab }} </td>
                                @php
                                    // Konversi start_date ke objek Carbon
                                    $startDate = \Carbon\Carbon::parse($p->tgl_mulai);
                                    $frekuensi = $p->frekuensi_perawatan;

                                    // Hitung bulan perawatan berdasarkan start_date dan frekuensi
                                    $bulanPerawatan = [];
                                    $currentDate = $startDate->copy();
                                    while ($currentDate->year === $startDate->year) {
                                        $bulanPerawatan[] = $currentDate->month;
                                        $currentDate->addMonths($frekuensi);
                                    }
                                @endphp

                                @foreach ($bulan as $index => $b)
                                    <td class="{{ in_array($index + 1, $bulanPerawatan) ? 'bg-primary' : '' }}"></td>
                                @endforeach
                            </tr>
                        @endforeach


                    </tbody>

                </table>
            </div>

            <form action="{{ route('hrga8_1.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="{{ $title }}" size="modal-xl" btnSave="Y">
                    <div class="row">

                        <div class="col-lg-2">
                            <label for="">Lokasi</label>
                            <select name="lokasi_id" id="" class="select2 lokasi">
                                <option value="">Pilih Lokasi</option>
                                @foreach ($lokasi as $l)
                                    <option value="{{ $l->id }}">{{ $l->lokasi }} lantai
                                        ({{ $l->lantai }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Nama Mesin</label>
                            <select name="id_item" id="" class="select2 item">

                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Frekuensi Perawatan </label>
                            <input type="number" class="form-control" name="frekuensi_perawatan"
                                placeholder="Dalam hitungan bulan">
                        </div>
                        <div class="col-lg-2 mt-2">
                            <label for="">Tanggal Pelaksanaan</label>
                            <input type="date" class="form-control" name="tgl_mulai">
                        </div>
                        <div class="col-lg-2 mt-2">
                            <label for="">Penanggung Jawab</label>
                            <input type="text" class="form-control" name="penanggung_jawab">
                        </div>



                    </div>
                </x-theme.modal>
            </form>

        </section>







        @section('scripts')
            <script>
                $(document).ready(function() {
                    $(document).on('click', '.btn_selesai', function() {
                        var id = $(this).attr('id');

                        $('.id_permintaan').val(id);

                    })
                    $(document).on('change', '.lokasi', function() {
                        var id = $(this).val();


                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga8_1.get_item') }}",
                            data: {
                                id: id
                            },
                            success: function(response) {
                                $('.item').html(response);
                            }
                        });

                    })


                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
