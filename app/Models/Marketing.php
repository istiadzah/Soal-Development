<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    use HasFactory;
    protected $table = 'marketing';
    protected $fillable = [
        'name'
    ];

    function penjualan() {
        return $this->hasMany(Penjualan::class);
    }

    function perhitungan() {
        return $this->hasMany(Perhitungan::class);
    }
}
