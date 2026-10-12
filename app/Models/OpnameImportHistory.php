<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpnameImportHistory extends Model
{
    protected $guarded = [];

    protected $casts = ['invoices' => 'array', 'rows' => 'array'];
}
