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
                            <th width="5%">No</th>
                            <th width="30%">Dept / Activity</th>
                            <th width="70%">Kebutuhan Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>HRD GA</td>
                            <td>
                                <a href="{{ route('hccp.sampleAdministrator') }}">* Sampel Administrasi Proses
                                    Rekruitmen </a>
                                <br>
                                <a href="">* Evaluasi Kompetensi Karyawan</a> <br>
                                <a href="">* Hasil Medical Checkup</a> <br>
                                <a href="">* Program & Checklist pemeliharaan bangunan & Infrastruktur</a> <br>
                                <a href="">* Program & Checklist cleaning & sanitasi</a> <br>
                                <a href="">* Rekap Penerimaan Tamu</a> <br>
                                <a href="">* Dokumen Perijinan dan Laporan Aktifitas pest Control : kontrk, list
                                    pestisida, laporan treatment, laporan perbaikan dari setiap rekomendasi, bukti
                                    training teknisi</a> <br>
                                <a href="">* Catatan Pembuangan Limbah B3 : termasuk kontrak pihak ke-3 yg
                                    mengambil limbah B3, organik (bulu)</a> <br>
                                <a href="">* Dokumentasi pemeliharaan CCTV : SOP & laporan pencatatan</a> <br>
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
