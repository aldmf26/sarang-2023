<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulir extends Model
{
    use HasFactory;
    protected $table = 'formulir_sarang';
    protected $guarded = [];
    protected $primaryKey = 'id_formulir';

    public function pemberi()
    {
        return $this->belongsTo(User::class, 'id_pemberi', 'id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'id_penerima', 'id');
    }
}
