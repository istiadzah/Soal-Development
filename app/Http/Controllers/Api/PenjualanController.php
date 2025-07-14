<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualan = Penjualan::orderBy('id')->get();

        $penjualan->transform(function($item) {
            $item->cargo_fee = $this->NumberFormat($item->cargo_fee);
            $item->total_balance = $this->NumberFormat($item->total_balance);
            $item->grand_total = $this->NumberFormat($item->grand_total);
            return $item;
        });
        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => $penjualan,
        ]);
    }

    public function TransactionNumber()
    {
        $count = Penjualan::count();
        $nextNumber = $count + 1;
        return 'TRX' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function NumberFormat($amount){
        if ($amount === null || $amount === '') {
            return '0';
        }
        return "Rp " . number_format($amount, 0, ',', '.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'marketing_Id' => 'exists:marketing,id',
            'date'=> 'required|date',
            'total_balance' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Data gagal disimpan',
                'errors' => $validator->errors(),
            ]);
        }

        $penjualan = new Penjualan();
        $penjualan->transaction_number = $this->TransactionNumber();
        $penjualan->marketing_Id = $request->input('marketing_Id');
        $penjualan->date = $request->input('date');
        $penjualan->cargo_fee = $request->input('cargo_fee');
        $penjualan->total_balance = $request->input('total_balance');
        $penjualan->grand_total = $request->input('grand_total');

        $penjualan->save();

        $penjualan->cargo_fee = $this->NumberFormat($penjualan->cargo_fee);
        $penjualan->total_balance = $this->NumberFormat($penjualan->total_balance);
        $penjualan->grand_total = $this->NumberFormat($penjualan->grand_total);

        session(['marketing_Id' => $request->marketing_Id]);

            return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $penjualan,
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penjualan = Penjualan::find($id);

        if($penjualan) {
            $penjualan->cargo_fee = $this->NumberFormat($penjualan->cargo_fee);
            $penjualan->grand_total = $this->NumberFormat($penjualan->grand_total);
            $penjualan->total_balance = $this->NumberFormat($penjualan->total_balance);
            return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => $penjualan,
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $penjualan = Penjualan::find($id);
        if (empty($penjualan)){
            return response()->json([
                'status' => false,
                'message' => 'Data Tidak Ditemukan'
            ]);
        }
        $validator = Validator::make($request->all(),[

            'marketing_Id' => 'exists:marketing,id',
            'date'=> 'required|date',
            'total_balance' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Data gagal disimpan',
                'errors' => $validator->errors(),
            ]);
        }

        $penjualan->transaction_number = $this->TransactionNumber();
        $penjualan->marketing_Id = $request->input('marketing_Id');
        $penjualan->date = $request->input('date');
        $penjualan->cargo_fee = $request->input('cargo_fee');
        $penjualan->total_balance = $request->input('total_balance');
        $penjualan->grand_total = $request->input('grand_total');

        $penjualan->save();

        $penjualan->cargo_fee = $this->NumberFormat($penjualan->cargo_fee);
        $penjualan->total_balance = $this->NumberFormat($penjualan->total_balance);
        $penjualan->grand_total = $this->NumberFormat($penjualan->grand_total);

        session(['marketing_Id' => $request->marketing_Id]);

            return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $penjualan,
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penjualan = Penjualan::find($id);
        if (empty($penjualan)){
            return response()->json([
                'status' => false,
                'message' => 'Data Tidak Ditemukan'
            ]);
        }
        $penjualan->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data Berhasil Dihapus'
        ]);
    }
}
