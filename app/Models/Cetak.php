<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cetak extends Model
{
    use HasFactory;
    protected $table = 'cetak_new';
    protected $guarded = [];
    
}
