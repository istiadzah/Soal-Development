<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Perhitungan extends Model
{
    use HasFactory;
    protected $table = 'perhitungan';
    protected $fillable = [
        'marketing_Id',
        'bulan',
        'omset',
        'komisi',
        'komisi_total'
    ];

    public function marketing() {
        return $this->belongsTo(Marketing::class, 'marketing_Id');
    }

    public function getBulanTextAttribute()
    {
        try {
            return Carbon::createFromFormat('Y-m', $this->bulan)->format('F Y');
        } catch (\Exception $e) {
            return $this->bulan;
        }
    }

    public function getFormattedOmsetAttribute()
    {
        return 'Rp ' . number_format($this->omset, 0, ',', '.');
    }

    public function getFormattedKomisiTotalAttribute()
    {
        return 'Rp ' . number_format($this->komisi_total, 0, ',', '.');
    }
    
    public function pembayaran()
{
    return $this->hasMany(Pembayaran::class);
}

public function getSisaKomisiAttribute()
{
    $dibayar = $this->pembayaran()->sum('jumlah');
    return max(0, $this->komisi_total - $dibayar);
}

public function getStatusPembayaranAttribute()
{
    return $this->sisa_komisi == 0 ? 'Lunas' : 'Belum Lunas';
}

}