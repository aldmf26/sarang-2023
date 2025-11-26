<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Label</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .tidak-cetak {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }
        }

        .label {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            width: 30%;
            display: inline-block;
            margin: 1%;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .logo {
            width: 50px;
            height: auto;
        }

        .label-content {
            font-size: 12px;
            text-align: left;
            line-height: 10px;
        }

        .signature-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .signature-table td {
            border: 1px solid black;
            text-align: center;
            height: 35px;
            font-size: 9px;
        }

        @media print {
            html {
                zoom: .85;
            }
        }
    </style>
</head>

<body>
    <center>
        <div class="p-2">
            <div class="row">

                @foreach ($formulir as $i => $d)
                    <div class="label mt-1">

                        <div class="header">
                            <div class="d-flex align-items-start">
                                <img src="{{ asset('img/logo.jpeg') }}" class="logo" alt="Logo"
                                    style="height:40px;">
                                <div class="flex-grow-1 text-center">
                                    <p style="font-size: 9px; margin:0;">PT. AGRIKA GATYA ARUM</p>
                                    <p style="font-size: 9px; margin:0;">
                                        <strong><u>Identitas Bahan Baku</u></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @php

                        @endphp

                        <table style="font-size: 9px; text-align: left">
                            <tr>
                                <td>Nama Bahan Baku</td>
                                <td>:</td>
                                <th>{{ $grades[$d->grade_id]['grade']['nama'] }}</th>
                                <td colspan="8">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>No Box</td>
                                <td>: {{ $d->no_box }}</td>
                            </tr>

                            <tr>
                                <td>Nama Produsen</td>
                                <td>:</td>
                                <td>{{ $rm_walet[$d->rwb_id]['rumah_walet']['nama'] }}</td>
                                <td colspan="8">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>Pcs</td>
                                <td>: {{ $d->pcs_awal }}</td>
                            </tr>

                            <tr>
                                <td>Tanggal Kedatangan</td>
                                <td>:</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td colspan="8">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>Gram</td>
                                <td>: {{ $d->gr_awal }}</td>
                            </tr>

                            <tr>
                                <td>Kode Lot</td>
                                <td>:</td>
                                <td>{{ $d->no_invoice }}</td>
                            </tr>

                            <tr>
                                <td>Kode Grading</td>
                                <td>:</td>
                                <td>{{ $grades[$d->grade_id]['grade']['kode'] }}</td>
                            </tr>

                            <tr>
                                <td>Keterangan</td>
                                <td>:</td>
                                <td>{{ substr($d->nm_partai, 3) }}</td>
                            </tr>
                        </table>

                        <table class="signature-table">
                            <tr>
                                <td>KEPALA GUDANG BAHAN BAKU</td>
                                <td>KEPALA QC</td>
                                <td>STATUS</td>
                            </tr>

                            <tr>
                                <td>
                                    {!! QrCode::size(40)->generate('https://ptagrikagatyaarum.com/verify-ttd/1069') !!}
                                </td>
                                <td>
                                    {!! QrCode::size(40)->generate('https://ptagrikagatyaarum.com/verify-ttd/1060') !!}
                                </td>
                                <td>
                                    PASS / REJECT <br>
                                    <span style="font-size: 8px">(Coret yang tidak perlu)</span>
                                </td>
                            </tr>
                        </table>

                    </div>

                    {{-- === PAGE BREAK SETIAP 9 LABEL === --}}
                    @if (($i + 1) % 9 == 0)
                        <div class="page-break"></div>
                    @endif
                @endforeach

            </div>
        </div>
    </center>

    <script>
        window.print();
    </script>
</body>

</html>
