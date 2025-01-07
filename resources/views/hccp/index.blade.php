<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead" width="5%">No</th>
                            <th class="dhead" width="30%">Dept / Activity</th>
                            <th class="dhead" width="70%">Kebutuhan Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>HRD GA</td>
                            <td>
                                @php
                                    $datas = [
                                        [
                                            'url' => 'hccp.sampleAdministrator',
                                            'teks' => '* Sampel Administrasi Proses Rekruitmen',
                                        ],
                                        [
                                            'url' => 'hccp.evaluasiKompetensiKaryawan',
                                            'teks' => '* Evaluasi Kompetensi Karyawan',
                                        ],
                                        [
                                            'url' => 'hccp.pelatihan',
                                            'teks' => '* Pelatihan (difolder ada di foto tidak ada)',
                                        ],
                                        [
                                            'url' => 'hccp.medical',
                                            'teks' => '* Hasil Medical Checkup',
                                        ],
                                        [
                                            'url' => 'hccp.pemeliharaanBangunan',
                                            'teks' => '* Program & Checklist pemeliharaan bangunan & Infrastruktur',
                                        ],
                                        [
                                            'url' => 'hccp.sanitasi',
                                            'teks' => '* Program & Checklist cleaning & sanitasi',
                                        ],
                                        [
                                            'url' => 'hccp.sampleAdministrator',
                                            'teks' => '* Rekap Penerimaan Tamu',
                                        ],
                                        [
                                            'url' => 'hccp.sampleAdministrator',
                                            'teks' =>
                                                '* Dokumen Perijinan dan Laporan Aktifitas pest Control : kontrk, list pestisida, laporan treatment, laporan perbaikan dari setiap rekomendasi, bukti training teknisi',
                                        ],
                                        [
                                            'url' => 'hccp.pembuangan_sampah',
                                            'teks' =>
                                                '* Catatan Pembuangan Limbah B3 : termasuk kontrak pihak ke-3 yg melaksanakan pembuangan',
                                        ],
                                        [
                                            'url' => 'hccp.sampleAdministrator',
                                            'teks' => '* Dokumentasi pemeliharaan CCTV : SOP & laporan pencatatan',
                                        ],
                                    ];
                                @endphp
                                @foreach ($datas as $d => $i)
                                    <a wire:navigate href="{{ route($i['url']) }}"
                                        class="text-start mb-2 btn btn-sm btn-outline-primary">{{ $i['teks'] }}</a>
                                    <br>
                                @endforeach


                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>


        </section>



        @section('scripts')
        @endsection
    </x-slot>

</x-theme.app>
