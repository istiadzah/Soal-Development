<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    
    protected $table = 'pembayaran';
    
    protected $fillable = [
        'perhitungan_id',
        'tanggal',
        'jumlah',
        
    ];

    public function perhitungan()
{
    return $this->belongsTo(Perhitungan::class);
}

    
}