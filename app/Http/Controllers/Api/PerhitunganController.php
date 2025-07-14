<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerhitunganResource; // Import Resource
use App\Models\Marketing;
use App\Models\Penjualan;
use App\Models\Perhitungan;
use Illuminate\Http\Request;

class PerhitunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->perhitunganKomisi();
        $perhitungan = Perhitungan::with('marketing')
        ->select('perhitungan.*')
        ->join('marketing', 'perhitungan.marketing_Id', '=', 'marketing.id')
        ->orderBy('perhitungan.bulan', 'desc')
        ->orderBy('marketing.name', 'asc')
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => PerhitunganResource::collection($perhitungan),
        ]);
    }

    public function perhitunganKomisi(){
        $periode = Penjualan::selectRaw('YEAR(date) as year, MONTH(date) as month')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $marketings = Marketing::all();

        foreach($periode as $periodes){
            $bulan = sprintf('%04d-%02d', $periodes->year, $periodes->month);

            foreach($marketings as $marketing){
                $omset = Penjualan::where('marketing_Id', $marketing->id)
                ->whereYear('date', $periodes->year)
                ->whereMonth('date', $periodes->month)
                ->sum('total_balance');

                if($omset > 0){
                    $persen_komisi = $this->Komisi($omset);
                    $komisi_total = ($omset * $persen_komisi)/100;

                    Perhitungan::updateOrCreate(
                        [
                            'marketing_Id' => $marketing->id,
                            'bulan' => $bulan
                        ],
                        [
                            'omset' => $omset,
                            'komisi' => $persen_komisi,
                            'komisi_total' => $komisi_total,
                        ]
                    );
                }
            }
        }
    }

    public function formatAngka($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    public function Komisi($omset){
        if ($omset >= 0 && $omset <= 100000000) {
            return 0;
        }elseif ($omset > 100000000 && $omset <= 200000000) {
            return 2.5;
        }elseif ($omset > 200000000 && $omset <= 500000000) {
            return 5;
        }elseif ($omset > 500000000) {
            return 10;
        }
        return 0;
    }

    public function HitungKomisi(Request $request) {
        $request->validate([
            'bulan' =>'required|string|size:7|regex:/^\d{4}-\d{2}$/'
        ]);
        
        $bulan = $request->bulan;
        $tahun = substr($bulan, 0, 4);
        $bulanNumber = substr($bulan, 5, 2);

        $marketings = Marketing::all();
        $result = [];

        foreach ($marketings as $marketing){
            $omset = Penjualan::where('marketing_Id', $marketing->id)
            ->whereYear('date', $tahun)
            ->whereMonth('date', $bulanNumber)
            ->sum('total_balance');

            if($omset > 0){
                $persen_komisi = $this->Komisi($omset);
                $komisi_total = ($omset * $persen_komisi)/100;

                $perhitungan = Perhitungan::updateOrCreate(
                    [
                        'marketing_Id' => $marketing->id,
                        'bulan' => $bulan
                    ],
                    [
                        'omset' => $omset,
                        'komisi' => $persen_komisi,
                        'komisi_total' => $komisi_total,
                    ]
                );

                $result[] = [
                    'marketing' => $marketing->name,
                    'omset' => $this->formatAngka($omset), 
                    'omset_raw' => $omset,
                    'persen_komisi' => $persen_komisi,
                    'komisi_total' => $this->formatAngka($komisi_total), 
                    'komisi_total_raw' => $komisi_total,
                ];
            }
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihitung',
            'data' => $result,
        ]);
    }

    public function indexWithKomisi()
{
    $perhitungan = Perhitungan::with('marketing')
        ->where('komisi_total', '>', 0)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_marketing' => $item->marketing->name,
                'bulan' => $item->getBulanText(),
                'komisi_total' => $item->formatRupiah($item->komisi_total),
                'status_pembayaran' => $item->getStatusPembayaran(),
            ];
        });

    return response()->json([
        'status' => true,
        'message' => 'Data marketing yang menerima komisi berhasil diambil',
        'data' => $perhitungan
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'marketing_Id' => 'required|exists:marketing,id',
            'bulan' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
            'omset' => 'required|integer|min:0',
            'komisi' => 'required|numeric|min:0',
            'komisi_total' => 'required|integer|min:0'
        ]);

        $perhitungan = Perhitungan::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Data perhitungan berhasil ditambahkan',
            'data' => new PerhitunganResource($perhitungan->load('marketing')) 
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perhitungan = Perhitungan::with('marketing')->find($id);
        
        if (!$perhitungan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PerhitunganResource($perhitungan) 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}