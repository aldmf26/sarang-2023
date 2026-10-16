<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpnameBulananInvoice extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['transaksi' => 'array', 'locked_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function lines()
    {
        return $this->hasMany(OpnameBulananLine::class, 'invoice_id')->orderBy('id');
    }

    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
