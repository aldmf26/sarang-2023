<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Label Bahan Baku</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        .toolbar {
            padding: 10px;
            text-align: center;
        }

        .sheet {
            display: grid;
            width: 287mm;
            height: 198mm;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, minmax(0, 1fr));
            gap: 2mm;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
        }

        .packing .sheet {
            width: 287mm;
            height: 142mm;
            grid-template-rows: repeat(3, 46mm);
            align-content: start;
        }

        .packing .label {
            padding: 1mm;
        }

        .packing .label-header {
            grid-template-columns: 10mm 1fr 10mm;
            margin-bottom: .5mm;
        }

        .packing .logo {
            width: 8mm;
            height: 8mm;
        }

        .packing .company {
            font-size: 7px;
            line-height: 1.15;
        }

        .packing .group-badge {
            width: 7mm;
            height: 7mm;
            font-size: 9px;
        }

        .packing .identity {
            font-size: 7.5px;
            line-height: 1.05;
        }

        .packing .identity td {
            padding: .4mm 0;
        }

        .packing .identity .field {
            width: 20mm;
        }

        .packing .identity .value {
            width: 17mm;
        }

        .packing .identity .detail-field {
            width: 9mm;
        }

        .packing .approval {
            font-size: 5.5px;
        }

        .packing .approval th {
            height: 5.5mm;
            padding: .5mm;
        }

        .packing .approval td {
            height: 9.5mm;
        }

        .packing .qr svg {
            width: 8mm;
            height: 8mm;
        }

        .packing .status-note {
            font-size: 5px;
        }

        .sheet:last-child {
            page-break-after: auto;
        }

        .label {
            display: flex;
            min-width: 0;
            overflow: hidden;
            flex-direction: column;
            border: 1px solid #000;
            padding: 2mm;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .label-header {
            display: grid;
            grid-template-columns: 15mm 1fr 15mm;
            align-items: start;
            margin-bottom: 1mm;
        }

        .logo {
            width: 13mm;
            height: 13mm;
            object-fit: contain;
        }

        .company {
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.3;
        }

        .company-title {
            text-decoration: underline;
        }

        .group-badge {
            display: flex;
            width: 9mm;
            height: 9mm;
            align-items: center;
            justify-content: center;
            justify-self: end;
            overflow: hidden;
            border: 1px solid #000;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            text-align: center;
        }

        .identity {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            line-height: 1.15;
        }

        .identity td {
            padding: .6mm 0;
            vertical-align: top;
        }

        .identity .field {
            width: 27mm;
        }

        .identity .separator {
            width: 3mm;
        }

        .identity .value {
            width: 24mm;
            overflow-wrap: anywhere;
        }

        .identity .spacer {
            width: 4mm;
        }

        .identity .detail-field {
            width: 12mm;
            white-space: nowrap;
        }

        .approval {
            width: 100%;
            margin-top: auto;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7px;
            break-inside: avoid;
            page-break-inside: avoid;
            flex-shrink: 0;
        }

        .approval th,
        .approval td {
            border: 1px solid #000;
            text-align: center;
        }

        .approval th {
            height: 8mm;
            padding: 1mm;
            font-weight: 400;
        }

        .approval td {
            height: 13mm;
        }

        .qr svg {
            display: block;
            width: 11mm;
            height: 11mm;
            margin: 0 auto;
        }

        .status-note {
            font-size: 6.5px;
        }

        @media print {
            .toolbar {
                display: none;
            }
        }
    </style>
</head>

<body class="{{ $isPacking ? 'packing' : 'standard' }}">
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print</button>
    </div>

    @foreach ($labels->chunk(9) as $page)
        <section class="sheet">
            @foreach ($page as $label)
                <article class="label">
                    <header class="label-header">
                        <img src="{{ asset('img/logo.jpeg') }}" class="logo" alt="Logo PT Agrika Gatya Arum">
                        <div class="company">
                            <div>PT. AGRIKA GATYA ARUM</div>
                            <div class="company-title">Identitas Bahan Baku</div>
                        </div>
                        <div class="group-badge">{{ $label->kelompok ?: '-' }}</div>
                    </header>

                    <table class="identity">
                        <tr>
                            <td class="field">Nama Bahan Baku</td>
                            <td class="separator">:</td>
                            <td class="value" colspan="4"><strong>{{ $label->grade }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Nama Produsen</td>
                            <td>:</td>
                            <td>{{ $labelInfo['nama_produsen'] }}</td>
                            <td></td>
                            <td>Partai</td>
                            <td>:</td>
                            <td>{{ $label->partai }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Kedatangan</td>
                            <td>:</td>
                            <td>{{ date('d-m-Y', strtotime($labelInfo['tanggal_kedatangan'])) }}</td>
                            <td></td>
                            <td>No Box</td>
                            <td>:</td>
                            <td>{{ $label->box }}</td>
                        </tr>
                        <tr>
                            <td>Kode Lot</td>
                            <td>:</td>
                            <td>{{ $labelInfo['kode_lot'] }}</td>
                            <td></td>
                            <td>Pcs</td>
                            <td>:</td>
                            <td>{{ number_format($label->pcs, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Kode Grading</td>
                            <td>:</td>
                            <td>{{ $labelInfo['kode_grading'] }}</td>
                            <td></td>
                            <td>Gr</td>
                            <td>:</td>
                            <td>{{ number_format($label->gr, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>:</td>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                    </table>

                    <table class="approval">
                        <tr>
                            <th>KEPALA GUDANG BAHAN BAKU</th>
                            <th>KEPALA QC</th>
                            <th>STATUS</th>
                        </tr>
                        <tr>
                            <td class="qr">
                                {!! QrCode::size(60)->generate('https://ptagrikagatyaarum.com/verify-ttd/1069') !!}
                            </td>
                            <td class="qr">
                                {!! QrCode::size(60)->generate('https://ptagrikagatyaarum.com/verify-ttd/1060') !!}
                            </td>
                            <td>
                                PASS / <s>REJECT</s><br>
                                <span class="status-note">(Coret yang tidak perlu)</span>
                            </td>
                        </tr>
                    </table>
                </article>
            @endforeach
        </section>
    @endforeach

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>

</html>
