<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PO Opname — {{ $invoice->no_invoice }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; background: #eceff2; font: 12px Arial, sans-serif; }
        .toolbar { max-width: 1120px; margin: 18px auto; display: flex; justify-content: space-between; gap: 12px; padding: 0 16px; }
        .toolbar a, .toolbar button { font: inherit; text-decoration: none; color: #fff; background: #285b93; border: 0; border-radius: 4px; padding: 10px 18px; cursor: pointer; }
        .toolbar a { background: #59636e; }
        .sheet { max-width: 1120px; margin: 0 auto 24px; padding: 28px; background: #fff; }
        .heading { border-bottom: 2px solid #111; text-align: center; padding-bottom: 12px; margin-bottom: 16px; }
        .heading .company { font-size: 13px; font-weight: bold; letter-spacing: 1px; }
        h1 { font-size: 20px; margin: 6px 0 4px; }
        .number { font-size: 13px; }
        .metadata { display: flex; gap: 32px; margin-bottom: 18px; }
        .metadata table { width: 50%; border-collapse: collapse; }
        .metadata th { width: 130px; text-align: left; font-weight: normal; }
        .metadata td, .metadata th { padding: 4px 0; vertical-align: top; }
        .metadata td::before { content: ': '; }
        .counts { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 11px; }
        .counts th, .counts td { border: 1px solid #555; padding: 7px 5px; vertical-align: middle; overflow-wrap: anywhere; }
        .counts th { background: #f1f3f5; text-align: center; }
        .counts .num { text-align: right; white-space: nowrap; }
        .counts .center { text-align: center; }
        .counts .total { font-weight: bold; background: #f1f3f5; }
        .note { margin-top: 12px; white-space: pre-wrap; overflow-wrap: anywhere; }
        .signatures { display: flex; justify-content: space-between; gap: 24px; margin-top: 30px; break-inside: avoid; }
        .signature { width: 30%; text-align: center; }
        .signature .name { margin: 58px 0 0; padding-top: 5px; border-top: 1px solid #333; }
        .stamp { margin-top: 18px; color: #555; font-size: 10px; }
        @media (max-width: 700px) { .sheet { padding: 12px; overflow-x: auto; } .metadata { gap: 12px; } .metadata th { width: 80px; } .counts { min-width: 850px; } }
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { max-width: none; margin: 0; padding: 0; overflow: visible; }
            .counts { min-width: 0; font-size: 10px; }
            .counts thead { display: table-header-group; }
            .counts tbody tr { break-inside: avoid; page-break-inside: avoid; }
            .metadata, .heading, .note { break-inside: avoid; }
            .counts th, .counts .total { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('opname_bulanan.show', $invoice->id) }}">Kembali ke invoice</a>
        <button type="button" onclick="window.print()">Cetak PO</button>
    </div>
    <main class="sheet">
        <div class="heading">
            <div class="company">KERJA SARANG</div>
            <h1>PO OPNAME BULANAN</h1>
            <div class="number">{{ $invoice->no_invoice }}</div>
        </div>
        <div class="metadata">
            <table>
                <tr><th>Periode</th><td>{{ \Carbon\Carbon::parse($invoice->bulan)->format('m-Y') }}</td></tr>
                <tr><th>Tanggal opname</th><td>{{ \Carbon\Carbon::parse($invoice->tanggal)->format('d-m-Y') }}</td></tr>
                <tr><th>Posisi stok</th><td>{{ $position }}</td></tr>
                <tr><th>Nama partai</th><td>{{ $invoice->nm_partai ?? '—' }}</td></tr>
                <tr><th>Pembuat</th><td>{{ $invoice->pembuat?->name ?? '—' }}</td></tr>
            </table>
            <table>
                <tr><th>Pengawas</th><td>{{ $invoice->pengawas?->name ?? '—' }}</td></tr>
                <tr><th>Tujuan pengiriman</th><td>{{ $invoice->tujuan }}</td></tr>
                <tr><th>Status</th><td>{{ ['draft' => 'DRAFT', 'submitted' => 'DIAJUKAN', 'locked' => 'TERKUNCI'][$invoice->status] }}</td></tr>
                <tr><th>No. pengiriman</th><td>{{ $invoice->no_pengiriman ?? 'Belum dibuat' }}</td></tr>
            </table>
        </div>
        @php $locked = $invoice->status === 'locked'; @endphp
        <table class="counts">
            <colgroup>
                <col style="width:6%"><col style="width:8%"><col style="width:{{ $locked ? '18%' : '26%' }}">
                <col><col><col><col><col><col>
                @if ($locked)<col><col>@endif
            </colgroup>
            <thead>
                <tr>
                    <th>Baris</th><th>No. box asal</th><th>Partai</th><th>Grade asal</th><th>Grade real</th>
                    <th>Pcs sistem</th><th>Pcs real</th><th>Gram sistem</th><th>Gram real</th>
                    @if ($locked)<th>Box baru<br>posisi asal</th><th>Box kirim</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->lines as $line)
                    <tr>
                        <td class="center">{{ $line->baris }}</td><td class="center">{{ $line->no_box }}</td>
                        <td>{{ $line->nm_partai }}</td><td>{{ $line->grade_sistem ?? '—' }}</td><td>{{ $line->grade_real }}</td>
                        <td class="num">{{ number_format($line->pcs_sistem, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($line->pcs_real, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($line->gr_sistem, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($line->gr_real, 0, ',', '.') }}</td>
                        @if ($locked)<td class="center">{{ $line->no_box_baru }}</td><td class="center">{{ $line->no_box_kirim }}</td>@endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $locked ? 11 : 9 }}" class="center">Belum ada box dalam invoice.</td></tr>
                @endforelse
                <tr class="total">
                    <td colspan="5">TOTAL — {{ $invoice->lines->count() }} BOX</td>
                    <td class="num">{{ number_format($invoice->lines->sum('pcs_sistem'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($invoice->lines->sum('pcs_real'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($invoice->lines->sum('gr_sistem'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($invoice->lines->sum('gr_real'), 0, ',', '.') }}</td>
                    @if ($locked)<td colspan="2"></td>@endif
                </tr>
            </tbody>
        </table>
        @if ($invoice->catatan)<div class="note"><strong>Catatan:</strong> {{ $invoice->catatan }}</div>@endif
        <div class="signatures">
            <div class="signature">Dihitung / dicatat oleh<div class="name">{{ $invoice->pembuat?->name ?? '________________' }}</div></div>
            <div class="signature">Diperiksa pengawas<div class="name">{{ $invoice->pengawas?->name ?? '________________' }}</div></div>
            <div class="signature">Disetujui / dikunci oleh<div class="name">{{ $approvedBy ?? '________________' }}</div></div>
        </div>
        @if ($invoice->locked_at)<div class="stamp">Invoice dikunci pada {{ $invoice->locked_at->format('d-m-Y H:i') }}.</div>@endif
    </main>
</body>
</html>
