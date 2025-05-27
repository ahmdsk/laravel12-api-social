<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->get();
        return response()->json([
            'data' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa string.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'content.required' => 'Konten wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat jpeg, png, atau jpg.',
            'image.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        $data = $request->only('title', 'content');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/posts', $imageName);
            $data['image'] = $imageName;
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $data['image'] ?? null,
        ]);

        return response()->json([
            'message'   => 'Berhasil membuat postingan',
            'data'  => $post
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Postingan tidak ditemukan'], 404);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Kamu tidak berhak mengedit postingan ini'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa string.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'content.required' => 'Konten wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat jpeg, png, atau jpg.',
            'image.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        $data = $request->only('title', 'content');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/posts', $imageName);
            $data['image'] = $imageName;
        }

        $post->update($data);

        return response()->json([
            'message'   => 'Berhasil update postingan',
            'data'  => $data
        ]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Postingan tidak ditemukan'], 404);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Kamu tidak berhak menghapus postingan ini'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Berhasil menghapus postingan']);
    }
}
