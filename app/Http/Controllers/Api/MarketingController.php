<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marketing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Marketing::orderBy('id')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $marketing = new Marketing();
        $rules = [
            'name' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal disimpan',
                'data' => $validator->errors(),
            ]);
        }

        
        $marketing->name = $request->name;
        $marketing->save();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $marketing,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Marketing::find($id);
        if ($data){
            return response()->json([
                'status' => true,
                'message' => 'Data ditemukan',
                'data' => $data,
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }
        ;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $marketing = Marketing::find($id);
        if (empty($marketing)){
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                ]);
                }


        $rules = [
            'name' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal disimpan',
                'data' => $validator->errors(),
            ]);
        }

        
        $marketing->name = $request->name;
        $marketing->save();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $marketing = Marketing::find($id);
        if (empty($marketing)){
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                ]);
                }

        $marketing->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
