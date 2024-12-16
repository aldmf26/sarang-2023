<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilWawancaraModel extends Model
{
    use HasFactory;
    protected $table = 'hasil_wawancara';
    protected $guarded = [];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id');
    }

    public function anak()
    {
        return $this->belongsTo(Anak::class, 'id_anak', 'id_anak');
    }
    
    
}
