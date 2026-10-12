<?php

namespace App\Services;

class OpnameBkImport extends OpnameCabutImport
{
    public const STATUSES = ['bk' => 'BK sisa'];

    public const HEADERS = [
        'no_box', 'nm_partai', 'tipe', 'ket', 'warna', 'tgl', 'pcs_awal', 'gr_awal', 'hrga_satuan',
    ];

    public function validateRows(array $rows, string $status): array
    {
        foreach ($rows as &$row) {
            $row['id_pengawas'] = 0;
        }
        unset($row);

        return parent::validateRows($rows, $status);
    }

    protected function rules(): array
    {
        return array_replace(parent::rules(), ['id_pengawas' => 'required|integer|in:0']);
    }

    protected function startRow(array $row, object $user, int $giver, array &$invoices, int &$nextInvoice): void
    {
        $this->createBk($row, $user, null);
    }
}
