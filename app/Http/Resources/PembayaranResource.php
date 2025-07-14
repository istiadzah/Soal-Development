<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_marketing' => $this->perhitungan->marketing->name,
            'bulan' => $this->perhitungan->bulan_text,
            'komisi_total' => $this->perhitungan->formatted_komisi_total,
            'nominal_bayar' => $this->formatted_nominal_bayar,
            'metode_pembayaran' => ucfirst($this->metode_pembayaran),
            'tanggal_pembayaran' => $this->tanggal_pembayaran ? $this->tanggal_pembayaran->format('d/m/Y') : null,
            'keterangan' => $this->keterangan,
            'sisa_pembayaran' => $this->perhitungan->formatted_sisa_pembayaran,
            'status_pembayaran' => $this->perhitungan->status_pembayaran,
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null,
        ];
    }
}