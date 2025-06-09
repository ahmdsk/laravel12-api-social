<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $items = Items::with(['user'])
            ->where('user_id', $auth->id)
            ->latest()
            ->get();

        return response()->json([
            'data'  => $items,
        ]);
    }

    public function store(Request $request)
    {
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'title'       => 'required|max:255',
            'description' => 'nullable',
            'price'       => 'nullable',
            'image'       => 'nullable',
            'category'    => 'nullable',
        ], [
            'title.required'       => 'Title wajib diisi.',
            'title.max'            => 'Title tidak boleh lebih dari 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'  => $validator->errors()->first(),
            ], 422);
        }

        // Jika ada file gambar, simpan dan ambil URL-nya
        $imageUrl = null;
        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            // Mendapatkan URL lengkap untuk gambar yang diunggah dengan path nya contoh nya 127.0.0.1:8000/storage/images/nama_file.jpg
            $imageUrl = url('storage/' . $imagePath);
        } else {
            $imageUrl = null;
        }

        $item = Items::create([
            'user_id'     => $auth->id,
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'image_url'   => $imageUrl,
            'category'    => $request->category,
        ]);

        return response()->json([
            'message' => 'Item created successfully.',
            'data'    => $item,
        ], 201);
    }

    public function show($id)
    {
        $item = Items::with(['user'])->findOrFail($id);

        return response()->json([
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = Auth::user();
        $item = Items::findOrFail($id);

        if ($item->user_id !== $auth->id) {
            return response()->json([
                'message' => 'Kamu bukan pemilik item ini dan tidak memiliki izin untuk mengeditnya.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|max:255',
            'description' => 'nullable',
            'price'       => 'nullable',
            'image'       => 'nullable',
            'category'    => 'nullable',
        ], [
            'title.required'       => 'Title wajib diisi.',
            'title.max'            => 'Title tidak boleh lebih dari 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'  => $validator->errors()->first(),
            ], 422);
        }

        // Jika ada file gambar, simpan dan ambil URL-nya
        $imageUrl = null;
        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            // Mendapatkan URL lengkap untuk gambar yang diunggah dengan path nya contoh nya
            $imageUrl = url('storage/' . $imagePath);
        } else {
            $imageUrl = $item->image_url; // Tetap gunakan URL gambar lama jika tidak ada gambar baru
        }

        $item->update([
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'image_url'   => $imageUrl,
            'category'    => $request->category,
        ]);

        return response()->json([
            'message' => 'Item berhasil diperbarui.',
            'data'    => $item,
        ]);
    }

    public function destroy($id)
    {
        $auth = Auth::user();
        $item = Items::findOrFail($id);

        if ($item->user_id !== $auth->id) {
            return response()->json([
                'message' => 'Kamu bukan pemilik item ini dan tidak memiliki izin untuk menghapusnya.',
            ], 403);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus.',
        ]);
    }
}
