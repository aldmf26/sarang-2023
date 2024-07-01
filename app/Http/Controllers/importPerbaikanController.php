<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class importPerbaikanController extends Controller
{
    public function importperbaikan(Request $r)
    {

        // Memastikan file di-upload
        $file = $r->file('file');
        if (!$file) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Memastikan file dapat dibaca
        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        // Mengubah sheet menjadi array
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        if (empty($sheetData) || count($sheetData) < 2) {
            return redirect()->back()->with('error', 'Data dalam file kosong atau tidak valid.');
        }

        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                // Memastikan baris tidak kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Memastikan semua indeks tersedia di dalam array
                if (!isset($row[0], $row[1], $row[2], $row[3])) {
                    return redirect()->back()->with('error', 'Format data tidak valid.');
                }

                // Melakukan update ke database

                DB::table('bk')->where('id_bk', $row[0])->update([
                    'nm_partai' => $row[2],
                    'no_box' => $row[1],
                    'hrga_satuan' => $row[3],
                ]);
            }

            DB::commit();
            return redirect()->route('bk.index', ['kategori' => 'sortir'])->with('sukses', 'Data berhasil diimport');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function importperbaikansortir(Request $r)
    {

        // Memastikan file di-upload
        $file = $r->file('file');
        if (!$file) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Memastikan file dapat dibaca
        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        // Mengubah sheet menjadi array
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        if (empty($sheetData) || count($sheetData) < 2) {
            return redirect()->back()->with('error', 'Data dalam file kosong atau tidak valid.');
        }

        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                // Memastikan baris tidak kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Memastikan semua indeks tersedia di dalam array
                if (!isset($row[0], $row[1])) {
                    return redirect()->back()->with('error', 'Format data tidak valid.');
                }

                // Melakukan update ke database

                DB::table('sortir')->where('id_sortir', $row[0])->update([
                    'no_box' => $row[1],
                ]);
            }

            DB::commit();
            return redirect()->route('bk.index', ['kategori' => 'sortir'])->with('sukses', 'Data berhasil diimport');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
