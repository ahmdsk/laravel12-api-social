<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Postingan tidak ditemukan'], 404);
        }

        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = $post->comments()->create([
            'comment' => $request->comment,
            'user_id' => Auth::id()
        ]);

        return response()->json($comment, 201);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Kamu tidak dapat menghapus komentar orang lain'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus'], 200);
    }
}
