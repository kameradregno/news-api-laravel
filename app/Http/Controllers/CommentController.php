<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentsResource;
use App\Models\comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
        $this->middleware(['pemilik-comment'])->only('update');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' =>  'required|exists:posts,id',
            'comments_content' => 'required'
        ]);

$user = Auth::user()->id;

        $comment = comments::create([
            'user_id' => $user,
            'post_id' => $request->post_id,
            'comments_content' => $request->comments_content
        ]);

        return new CommentsResource($comment);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comments_content' => 'required'
        ]);

        $comment = comments::findOrFail($id);
        $comment->update($request->all());

        return new CommentsResource($comment);
    }

    public function delete($id)
    {
        $comment = comments::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'komentar dihapus'
        ]);
    }
}