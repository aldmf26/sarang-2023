import { FileBlob, SpreadsheetFile } from "@oai/artifact-tool";

const source = "G:/My Drive/2025/OPNAME AGA 2026 (Nanda & Aldi)/opname 5 agustus 2026/data import/wip1.xlsx";
const workbook = await SpreadsheetFile.importXlsx(await FileBlob.load(source));
const sheet = workbook.worksheets.getItem("Sheet1");
const rows = sheet.getRange("A2:G322").values;
const boxes = rows.map((row) => String(row[1]).trim()).filter(Boolean);
const totals = rows.reduce((sum, row) => ({
  pcs: sum.pcs + Number(row[3] || 0),
  gr: sum.gr + Number(row[4] || 0),
}), { pcs: 0, gr: 0 });
process.stdout.write(JSON.stringify({ count: boxes.length, boxes, totals }, null, 2));
