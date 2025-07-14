<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerhitunganResource extends JsonResource
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
            'nama_marketing' => $this->marketing->name,
            'bulan' => $this->bulan_text,
            'omset' => $this->formatted_omset,
            'komisi_persen' => $this->komisi . '%',
            'komisi_total' => $this->formatted_komisi_total,
        ];
    }
}
