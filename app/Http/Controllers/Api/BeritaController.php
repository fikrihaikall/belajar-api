<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Storage;
use Str;
use Validator;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $berita = Berita::with('kategori', 'tag', 'user')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar berita',
            'data' => $berita,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|unique:beritas',
            'deskripsi' => 'required',
            'foto' => 'required|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'id_user' => 'required',
            'tag' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // upload foto
            $path = $request->file('foto')->store('public/berita');

            $berita = new Berita();
            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->judul);
            $berita->deskripsi = $request->deskripsi;
            $berita->foto = $path;
            $berita->id_user = $request->id_user;
            $berita->id_kategori = $request->id_kategori;
            $berita->save();

            // melampirkan banyak tag
            $berita->tag()->attach($request->tag);
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dibuat',
                'data' => $berita,
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
            $berita = Berita::findOrFail($id)->with('kategori', 'tag', 'user')->first();
            return response()->json([
                'success' => true,
                'message' => 'Detail berita',
                'data' => $berita,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan',
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
            'judul' => 'required',
            'deskripsi' => 'required',
            'foto' => 'nullable|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'id_user' => 'required',
            'tag' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $berita = Berita::findOrFail($id);

            // hapus foto lama
            if ($request->hasFile('foto')) {
                Storage::delete($berita->foto);
                $path = $request->file('foto')->store('public/berita');
                $berita->foto = $path;
            }

            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->judul);
            $berita->deskripsi = $request->deskripsi;
            $berita->id_user = $request->id_user;
            $berita->id_kategori = $request->id_kategori;
            $berita->save();

            // melampirkan banyak tag
            $berita->tag()->sync($request->tag);
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil diperbarui',
                'data' => $berita,
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $berita = Berita::findOrFail($id);
            $berita->tag()->detach();
            // hapus foto
            Storage::delete($berita->foto);
            $berita->delete();
            return response()->json([
                'success' => true,
                'message' => 'berita ' . $berita->judul . ' berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);

        }
    }
}
