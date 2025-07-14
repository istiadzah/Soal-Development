<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan';
    protected $fillable = [
        'transaction_number',
        'marketing_Id',
        'date',
        'cargo_fee',
        'total_balance',
        'grand_total'
    ];

    public function marketing(){
        return $this->belongsTo(Marketing::class, 'marketing_Id');
    }

    function perhitungan() {
        return $this->hasMany(Perhitungan::class);
    }
}
