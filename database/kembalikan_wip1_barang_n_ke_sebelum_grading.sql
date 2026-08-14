-- Mengembalikan isi "wip1 barang N.xlsx" dari WIP1 ke Sisa belum grading.
-- Data proses sebelum grading (BK, cabut, cetak, sortir, dan formulir grade)
-- sengaja dipertahankan.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_wip1_barang_n;
CREATE TEMPORARY TABLE tmp_wip1_barang_n (
    no_box VARCHAR(50) NOT NULL PRIMARY KEY
);

INSERT INTO tmp_wip1_barang_n (no_box) VALUES
('14575'), ('14576'), ('14577'), ('14578'), ('14579'),
('14580'), ('14581'), ('14582'), ('14583'), ('14584'),
('14585'), ('14586'), ('14587'), ('14588'), ('14589'),
('14590'), ('14591'), ('14592'), ('14593'), ('14594'),
('14595'), ('14596'), ('14597'), ('14598'), ('14599'),
('14600'), ('14601'), ('14602'), ('14603'), ('14604');

-- Pemeriksaan sebelum perubahan: masing-masing hasil seharusnya 30.
SELECT COUNT(*) AS jumlah_grading_yang_akan_dihapus
FROM grading AS g
INNER JOIN tmp_wip1_barang_n AS t
    ON t.no_box = g.no_box_sortir
WHERE g.admin = 'IMPORT-WIP1-BARANG-N-20260814';

SELECT COUNT(*) AS jumlah_grading_partai_yang_akan_dihapus
FROM grading_partai AS gp
INNER JOIN tmp_wip1_barang_n AS t
    ON t.no_box = gp.box_pengiriman
WHERE gp.admin = 'IMPORT-WIP1-BARANG-N-20260814';

-- Lepaskan hasil grading partai terlebih dahulu, lalu proses grading-nya.
DELETE gp
FROM grading_partai AS gp
INNER JOIN tmp_wip1_barang_n AS t
    ON t.no_box = gp.box_pengiriman
WHERE gp.admin = 'IMPORT-WIP1-BARANG-N-20260814';

DELETE g
FROM grading AS g
INNER JOIN tmp_wip1_barang_n AS t
    ON t.no_box = g.no_box_sortir
WHERE g.admin = 'IMPORT-WIP1-BARANG-N-20260814';

-- Verifikasi posisi akhir. Hasil yang benar: 30 box, 771 pcs, 12.986 gr.
SELECT
    COUNT(DISTINCT fs.no_box) AS jumlah_box,
    SUM(fs.pcs_awal) AS pcs,
    SUM(fs.gr_awal) AS gr
FROM formulir_sarang AS fs
INNER JOIN tmp_wip1_barang_n AS t
    ON t.no_box = fs.no_box
WHERE fs.kategori = 'grade'
  AND NOT EXISTS (
      SELECT 1
      FROM grading AS g
      WHERE g.no_box_sortir = fs.no_box
        AND g.no_invoice IS NOT NULL
  );

COMMIT;

