-- Mengembalikan 80 box WIP2 yang dipilih ke stok home/gradingbj.
-- Data sebelum grading (BK, cabut, cetak, sortir, formulir grade) dipertahankan.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_box_wip2_kembali_grading;
CREATE TEMPORARY TABLE tmp_box_wip2_kembali_grading (
    box_pengiriman VARCHAR(100) NOT NULL PRIMARY KEY
);

INSERT INTO tmp_box_wip2_kembali_grading (box_pengiriman) VALUES
('11490'),('11498'),('11499'),('11500'),('11501'),('11551'),('11552'),('11554'),
('11557'),('11579'),('11587'),('11593'),('11594'),('11598'),('11599'),('11629'),
('11630'),('11644'),('11645'),('11647'),('11648'),('11649'),('11650'),('11651'),
('11652'),('11653'),('11654'),('11655'),('11657'),('11677'),('11678'),('11679'),
('11680'),('11681'),('11682'),('11683'),('11684'),('11715'),('11716'),('11717'),
('11719'),('11730'),('11738'),('11739'),('11740'),('11742'),('11745'),('11746'),
('11750'),('11747'),('11549'),('11550'),('11597'),('11558'),('11688'),('11591'),
('11725'),('11724'),('11743'),('11685'),('11736'),('11723'),('11690'),('11731'),
('11686'),('11737'),('11660'),('11726'),('11480'),('11536'),('11744'),('11590'),
('11720'),('11689'),('11721'),('11735'),('11722'),('11691'),('11732'),('11687');

DROP TEMPORARY TABLE IF EXISTS tmp_invoice_wip2_kembali_grading;
CREATE TEMPORARY TABLE tmp_invoice_wip2_kembali_grading (
    no_invoice VARCHAR(100) NOT NULL PRIMARY KEY
);

-- Ambil invoice hanya dari box yang benar-benar masih WIP2 dan belum dikirim.
INSERT INTO tmp_invoice_wip2_kembali_grading (no_invoice)
SELECT DISTINCT gp.no_invoice
FROM grading_partai AS gp
INNER JOIN tmp_box_wip2_kembali_grading AS target
    ON target.box_pengiriman = gp.box_pengiriman
WHERE gp.formulir = 'Y'
  AND gp.cek_qc = 'Y'
  AND gp.sudah_kirim = 'T'
  AND gp.no_invoice IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM pengiriman AS p
      WHERE p.no_box = gp.box_pengiriman
  );

DROP TEMPORARY TABLE IF EXISTS tmp_box_sumber_kembali_grading;
CREATE TEMPORARY TABLE tmp_box_sumber_kembali_grading (
    no_box VARCHAR(100) NOT NULL PRIMARY KEY
);

INSERT INTO tmp_box_sumber_kembali_grading (no_box)
SELECT DISTINCT g.no_box_sortir
FROM grading AS g
INNER JOIN tmp_invoice_wip2_kembali_grading AS target
    ON target.no_invoice = g.no_invoice;

-- PERIKSA sebelum perubahan.
-- Hasil yang benar: 80 output, 80 invoice, 80 box sumber,
-- 3.498 pcs dan 18.000 gr.
SELECT
    COUNT(DISTINCT gp.box_pengiriman) AS jumlah_output,
    COUNT(DISTINCT gp.no_invoice) AS jumlah_invoice,
    (SELECT COUNT(*) FROM tmp_box_sumber_kembali_grading) AS jumlah_box_sumber,
    SUM(CASE WHEN gp.grade <> 'susut' THEN gp.pcs ELSE 0 END) AS pcs,
    SUM(CASE WHEN gp.grade <> 'susut' THEN gp.gr ELSE 0 END) AS gr
FROM grading_partai AS gp
INNER JOIN tmp_invoice_wip2_kembali_grading AS target
    ON target.no_invoice = gp.no_invoice;

-- Bersihkan penanda proses setelah grading untuk output yang dipilih.
DELETE fs
FROM formulir_sarang AS fs
INNER JOIN tmp_box_wip2_kembali_grading AS target
    ON target.box_pengiriman = fs.no_box
WHERE fs.kategori IN ('wip', 'qc', 'wip2', 'pengiriman');

DELETE q
FROM qc AS q
INNER JOIN tmp_box_wip2_kembali_grading AS target
    ON target.box_pengiriman = q.box_pengiriman;

-- Bersihkan PO grading sumber, jika ada.
DELETE fs
FROM formulir_sarang AS fs
INNER JOIN tmp_box_sumber_kembali_grading AS sumber
    ON sumber.no_box = fs.no_box
WHERE fs.kategori = 'grading';

-- Lepaskan hasil grading partai dan hasil grading sumber.
DELETE gp
FROM grading_partai AS gp
INNER JOIN tmp_invoice_wip2_kembali_grading AS target
    ON target.no_invoice = gp.no_invoice;

DELETE g
FROM grading AS g
INNER JOIN tmp_invoice_wip2_kembali_grading AS target
    ON target.no_invoice = g.no_invoice;

-- Verifikasi akhir. Hasil yang benar: 80 box muncul sebagai stok grading,
-- total 3.498 pcs dan 18.000 gr.
SELECT
    COUNT(DISTINCT fs.no_box) AS jumlah_box_home_gradingbj,
    SUM(fs.pcs_awal) AS pcs,
    SUM(fs.gr_awal) AS gr
FROM formulir_sarang AS fs
INNER JOIN tmp_box_sumber_kembali_grading AS sumber
    ON sumber.no_box = fs.no_box
WHERE fs.kategori = 'grade'
  AND NOT EXISTS (
      SELECT 1
      FROM grading AS g
      WHERE g.no_box_sortir = fs.no_box
        AND g.no_invoice IS NOT NULL
  )
  AND NOT EXISTS (
      SELECT 1
      FROM formulir_sarang AS proses
      WHERE proses.no_box = fs.no_box
        AND proses.kategori = 'grading'
  );

COMMIT;
