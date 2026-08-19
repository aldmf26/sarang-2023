import { FileBlob, SpreadsheetFile } from '@oai/artifact-tool';

const source = String.raw`G:\My Drive\2025\OPNAME AGA 2026 (Nanda & Aldi)\opname 5 agustus 2026\data import\wip1 barang N.xlsx`;
const input = await FileBlob.load(source);
const workbook = await SpreadsheetFile.importXlsx(input);

const summary = await workbook.inspect({
  kind: 'workbook,sheet,table',
  maxChars: 12000,
  tableMaxRows: 30,
  tableMaxCols: 12,
  tableMaxCellChars: 120,
});

console.log(summary.ndjson);
