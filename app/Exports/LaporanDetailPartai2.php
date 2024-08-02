<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanDetailPartai2 implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $users = DB::table('users')->get();
        $users->transform(function ($user) {
            return [
                'id' => $user->id,
                'name_email' => $user->name . ' ' . $user->email,
            ];
        });
        return $users;
    }

    public function headings():array
    {
        return [
            'id',
            'name',
        ];
    }
}
