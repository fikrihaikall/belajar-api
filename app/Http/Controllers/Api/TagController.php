<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Str;
use Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tag = Tag::latest()->get();
        $res = [
            'success' => true,
            'message' => 'Daftar tag',
            'data' => $tag,
        ];
        return response()->json($res, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required|unique:tags',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = new Tag();
            $tag->nama_tag = $request->nama_tag;
            $tag->slug = Str::slug($request->nama_tag);
            $tag->save();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dibuat',
                'data' => $tag,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail tag',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = Tag::findOrFail($id);
            $tag->nama_tag = $request->nama_tag;
            $tag->slug = Str::slug($request->nama_tag);
            $tag->save();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data ' . $tag->nama_tag . ' berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);

        }

    }
}
