<x-theme.hccp_print :title="$title" :dok="$dok">
    <style>
        table {
            border-color: black;
        }
    </style>
    Bulan : 
    <table style="font-size: 13px" id="table1" class="table table-bordered border-dark">
        <thead>
            <tr>
                <th style="background-color: #D9D9D9">Nama Alat/Area</th>
                <th style="background-color: #D9D9D9">Identifikasi Alat/Area</th>
                <th style="background-color: #D9D9D9">Metode Sanitasi</th>
                <th style="background-color: #D9D9D9" class="text-center">Penanggung Jawab</th>
                <th style="background-color: #D9D9D9" class="text-center">Frekuensi</th>
                <th style="background-color: #D9D9D9" class="text-center">Sarana Cleaning</th>
                <th style="background-color: #D9D9D9">Sanitizer & Pengenceran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $d)
                <tr>
                    <td>{{ $d->nm_alat }}</td>
                    <td>{{ $d->identifikasi_alat }}</td>
                    <td>{{ $d->metode }}</td>
                    <td class="text-center">{{ $d->penanggung_jawab }}</td>
                    <td class="text-center">{{ $d->frekuensi }}</td>
                    <td class="text-center">{{ $d->sarana_cleaning }}</td>
                    <td>{{ $d->sanitizer }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-lg-4">
        </div>
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <table style="border: 1px solid black; border-collapse: collapse; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="border: 1px solid black; text-align: center;">Dibuat Oleh:</td>
                    <td style="border: 1px solid black; text-align: center;">Diketahui Oleh:</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: center; height: 80px; vertical-align: bottom;">[SPV. GA-IR]</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: bottom;">[KA.HRGA]</td>
                </tr>
            </table>
        </div>
    </div>
   
</x-theme.hccp_print>
