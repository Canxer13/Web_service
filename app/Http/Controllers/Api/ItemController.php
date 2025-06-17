<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item; // <-- Pastikan ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Suppor t\Facades\DB; // <-- DB facade sudah tidak kita perlukan di sini

class ItemController extends Controller
{
/**
     * @OA\Get(
     * path="/api/items",
     * tags={"Inventory"},
     * summary="Melihat semua data barang",
     * description="Mengambil daftar semua barang. Akses: Admin, Manager, Staff.",
     * security={{"bearerAuth": {"view-items"}}},
     * @OA\Response(
     * response=200,
     * description="Operasi berhasil",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Item"))
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * )
     * )
     */
    public function index()
    {
        // Kode Lama: $items = DB::table('items')->latest('created_at')->get();
        $items = Item::latest()->get(); // Lebih singkat
        return response()->json(['data' => $items]);
    }
     /**
     * @OA\Post(
     * path="/api/items",
     * tags={"Inventory"},
     * summary="Membuat barang baru",
     * description="Menambahkan barang baru ke inventaris. Akses: Admin, Manager.",
     * security={{"bearerAuth": {"create-items"}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "sku", "quantity", "price"},
     * @OA\Property(property="name", type="string", example="SSD NVMe 1TB"),
     * @OA\Property(property="sku", type="string", example="SSD-NVME-1TB"),
     * @OA\Property(property="quantity", type="integer", example=50),
     * @OA\Property(property="price", type="number", format="float", example=1200000)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Barang berhasil dibuat",
     * @OA\JsonContent(ref="#/components/schemas/Item")
     * ),
     * @OA\Response(response=403, description="Forbidden (Tidak punya izin)"),
     * @OA\Response(response=422, description="Data tidak valid")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:items|max:50',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Kode Lama: DB::table('items')->insertGetId([...]) dan query select lagi
        $item = Item::create($validator->validated()); // Satu baris ini sudah me-return objek Item yang baru

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }
    /**
     * @OA\Get(
     * path="/api/items/{id}",
     * tags={"Inventory"},
     * summary="Melihat barang spesifik",
     * description="Mengambil detail satu barang berdasarkan ID. Akses: Admin, Manager, Staff.",
     * security={{"bearerAuth": {"view-items"}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID dari barang",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Operasi berhasil",
     * @OA\JsonContent(ref="#/components/schemas/Item")
     * ),
     * @OA\Response(response=404, description="Barang tidak ditemukan")
     * )
     */
    public function show($id)
    {
        // Kode Lama: $item = DB::table('items')->where('id', $id)->first(); if(!$item){...}
        $item = Item::findOrFail($id); // findOrFail otomatis melempar 404 jika tidak ditemukan
        
        return response()->json(['data' => $item]);
    }
    /**
     * @OA\Put(
     * path="/api/items/{id}",
     * tags={"Inventory"},
     * summary="Mengupdate barang",
     * description="Memperbarui data barang yang sudah ada. Akses: Admin, Manager.",
     * security={{"bearerAuth": {"edit-items"}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID dari barang",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * description="Anda bisa mengirim hanya field yang ingin diubah.",
     * @OA\Property(property="name", type="string", example="SSD NVMe 1TB Gen4"),
     * @OA\Property(property="quantity", type="integer", example=45)
     * )
     * ),
     * @OA\Response(response=200, description="Update berhasil"),
     * @OA\Response(response=404, description="Barang tidak ditemukan"),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id); // Cari itemnya dulu, sekaligus validasi 404

        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|required|string|max:255',
            'sku'       => 'sometimes|required|string|max:50|unique:items,sku,' . $id,
            'quantity'  => 'sometimes|required|integer',
            'price'     => 'sometimes|required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Kode Lama: DB::table('items')->where('id', $id)->update([...]);
        $item->update($validator->validated()); // Panggil method update pada objek Item
        
        return response()->json([
            'message' => 'Item updated successfully',
            'data' => $item // $item sudah otomatis ter-refresh dengan data baru
        ]);
    }
      /**
     * @OA\Delete(
     * path="/api/items/{id}",
     * tags={"Inventory"},
     * summary="Menghapus barang",
     * description="Menghapus barang dari database. Akses: Hanya Admin.",
     * security={{"bearerAuth": {"delete-items"}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID dari barang yang akan dihapus",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Hapus berhasil",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Item deleted successfully"))
     * ),
     * @OA\Response(response=404, description="Barang tidak ditemukan"),
     * @OA\Response(response=403, description="Forbidden (Tidak punya izin)")
     * )
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id); // Cari itemnya
        
        // Kode Lama: DB::table('items')->where('id', $id)->delete();
        $item->delete(); // Panggil method delete pada objek Item
        
        return response()->json(['message' => 'Item deleted successfully'], 200);
    }
}