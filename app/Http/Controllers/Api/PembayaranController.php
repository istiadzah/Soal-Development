<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PembayaranResource;
use App\Models\Pembayaran;
use App\Models\Perhitungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perhitungan = Perhitungan::with('marketing', 'pembayaran')
            ->where('komisi_total', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_marketing' => $item->marketing->name,
                    'bulan' => $item->getBulanTextAttribute(),
                    'komisi_total' => $item->formatted_komisi_total,
                    'dibayarkan' => 'Rp ' . number_format($item->pembayaran->sum('jumlah'), 0, ',', '.'),
                    'sisa_komisi' => 'Rp ' . number_format($item->sisa_komisi, 0, ',', '.'),
                    'status' => $item->status_pembayaran
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran ditemukan',
            'data' => $perhitungan
        ]);
    }

    public function getPerhitunganBelumLunas()
    {
        $perhitungan = Perhitungan::with(['marketing', 'pembayaran'])
            ->get()
            ->filter(function ($item) {
                return !$item->isLunas();
            })
            ->map(function ($item) {
                $totalPembayaran = $item->getTotalPembayaran();
                $sisaPembayaran = $item->getSisaPembayaran();
                
                return [
                    'id' => $item->id,
                    'nama_marketing' => $item->marketing->name,
                    'bulan' => $item->getBulanText(),
                    'komisi_total' => $item->formatRupiah($item->komisi_total),
                    'total_pembayaran' => $item->formatRupiah($totalPembayaran),
                    'sisa_pembayaran' => $item->formatRupiah($sisaPembayaran),
                    'status_pembayaran' => $item->getStatusPembayaran(),
                    'display_text' => $item->marketing->name . ' - ' . $item->getBulanText() . ' (Sisa: ' . $item->formatRupiah($sisaPembayaran) . ')'
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'Data perhitungan belum lunas ditemukan',
            'data' => $perhitungan,
        ]);
    }

    public function getAllPembayaranMarketing()
    {
        $perhitungan = Perhitungan::with(['marketing', 'pembayaran'])
            ->where('komisi_total', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_marketing' => $item->marketing->name,
                    'bulan' => $item->getBulanText(),
                    'komisi_total' => $item->formatRupiah($item->komisi_total),
                    'total_pembayaran' => $item->formatRupiah($item->getTotalPembayaran()),
                    'status_pembayaran' => $item->getStatusPembayaran(),
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Data marketing yang menerima komisi (semua status)',
            'data' => $perhitungan,
        ]);
    }

    public function show(string $id)
    {
        $pembayaran = Pembayaran::with('perhitungan.marketing')->find($id);

        if (!$pembayaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil ditemukan',
            'data' => [
                'id' => $pembayaran->id,
                'marketing' => $pembayaran->perhitungan->marketing->name,
                'bulan' => $pembayaran->perhitungan->bulan,
                'jumlah' => 'Rp ' . number_format($pembayaran->jumlah, 0, ',', '.'),
                'tanggal' => $pembayaran->tanggal,
                'created_at' => $pembayaran->created_at,
                'updated_at' => $pembayaran->updated_at,
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'marketing_id' => 'required|exists:marketing,id',
            'bulan' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $perhitungan = Perhitungan::where('marketing_id', $request->marketing_id)
            ->where('bulan', $request->bulan)
            ->first();

        if (!$perhitungan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perhitungan tidak ditemukan untuk marketing dan bulan tersebut'
            ]);
        }

        $totalDibayar = $perhitungan->pembayaran()->sum('jumlah');
        $sisa = $perhitungan->komisi_total - $totalDibayar;

        if ($sisa <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Pembayaran komisi sudah lunas'
            ]);
        }

        if ($request->jumlah > $sisa) {
            return response()->json([
                'status' => false,
                'message' => 'Jumlah melebihi sisa komisi yang belum dibayarkan'
            ]);
        }

        $pembayaran = $perhitungan->pembayaran()->create([
            'tanggal' => now(),
            'jumlah' => $request->jumlah,
        ]);

        $totalDibayarBaru = $totalDibayar + $request->jumlah;
        $sisaBaru = $perhitungan->komisi_total - $totalDibayarBaru;

        return response()->json([
            'status' => true,
            'message' => 'Pembayaran berhasil disimpan',
            'data' => [
                'id' => $pembayaran->id,
                'perhitungan_id' => $pembayaran->perhitungan_id,
                'tanggal' => $pembayaran->tanggal,
                'jumlah' => 'Rp ' . number_format($pembayaran->jumlah, 0, ',', '.'),
                'komisi_total' => 'Rp ' . number_format($perhitungan->komisi_total, 0, ',', '.'),
                'total_dibayar' => 'Rp ' . number_format($totalDibayarBaru, 0, ',', '.'),
                'sisa_komisi' => 'Rp ' . number_format($sisaBaru, 0, ',', '.'),
                'status_lunas' => $sisaBaru <= 0 ? 'Lunas' : 'Belum Lunas',
            ]
        ]);
    }


    public function getBelumLunas()
    {
        $data = Perhitungan::with('marketing', 'pembayaran')
            ->get()
            ->filter(function ($item) {
                return $item->sisa_komisi > 0;
            })
            ->map(function ($item) {
                return [
                    'perhitungan_id' => $item->id,
                    'nama_marketing' => $item->marketing->name,
                    'bulan' => $item->getBulanTextAttribute(),
                    'komisi_total' => $item->formatted_komisi_total,
                    'dibayarkan' => 'Rp ' . number_format($item->pembayaran->sum('jumlah'), 0, ',', '.'),
                    'sisa_komisi' => 'Rp ' . number_format($item->sisa_komisi, 0, ',', '.')
                ];
            })->values();

        return response()->json([
            'status' => true,
            'message' => 'Daftar marketing yang belum lunas diambil',
            'data' => $data
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
        ]);

        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ]);
        }

        $perhitungan = $pembayaran->perhitungan;

        if (!$perhitungan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perhitungan tidak ditemukan'
            ]);
        }

        $totalPembayaranLain = $perhitungan->pembayaran()
            ->where('id', '!=', $pembayaran->id)
            ->sum('jumlah');

        $sisa = $perhitungan->komisi_total - $totalPembayaranLain;

        if ($request->jumlah > $sisa) {
            return response()->json([
                'status' => false,
                'message' => 'Jumlah melebihi sisa komisi yang belum dibayarkan'
            ]);
        }

        $pembayaran->jumlah = $request->jumlah;
        $pembayaran->save();

        $totalBaru = $totalPembayaranLain + $pembayaran->jumlah;
        $sisaBaru = $perhitungan->komisi_total - $totalBaru;

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil diperbarui',
            'data' => [
                'id' => $pembayaran->id,
                'perhitungan_id' => $pembayaran->perhitungan_id,
                'tanggal' => $pembayaran->tanggal,
                'jumlah' => 'Rp ' . number_format($pembayaran->jumlah, 0, ',', '.'),
                'komisi_total' => 'Rp ' . number_format($perhitungan->komisi_total, 0, ',', '.'),
                'total_dibayar' => 'Rp ' . number_format($totalBaru, 0, ',', '.'),
                'sisa_komisi' => 'Rp ' . number_format($sisaBaru, 0, ',', '.'),
                'status_lunas' => $sisaBaru <= 0 ? 'Lunas' : 'Belum Lunas',
            ]
        ]);
    }

    public function destroy(string $id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ]);
        }

        $pembayaran->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil dihapus'
        ]);
    }


}