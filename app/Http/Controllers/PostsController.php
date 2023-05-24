<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsDetailResource;
use App\Http\Resources\PostsResource;
use App\Models\posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only('store', 'update', 'delete');
        $this->middleware(['pemilik-news'])->only('update', 'delete');
    }

    public function index()
    {
        $posts = posts::all();
        // return response()->json(['news' => $posts]);
        return PostsResource::collection($posts);
    }

    public function show($id)
    {
        $post = posts::with('writer:id,username')->findOrFail($id);
        return new PostsDetailResource($post);
        // return response()->json(['news' => $post]);
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'news_content' => 'required'
        ]);

        if ($request->file) {
            $validated = $request->validate([
                'file' => 'mimes:jpg,jpeg,png|max:100000'
            ]);

            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();

            Storage::putFileAs('image', $request->file, $fileName . '.' . $extension);

            $request['image'] = $fileName.'.'.$extension;
            $request['author'] = Auth::user()->id;
            $post =  posts::create($request->all());
        }

        $request['author'] = Auth::user()->id;
        $post =  posts::create($request->all());

        return new PostsDetailResource($post->loadMissing('writer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'news_content' =>  'required|string'
        ]);

        $post = posts::findOrFail($id);
        $post->update($request->all());

        return new PostsDetailResource($post);
    }

    public function delete($id)
    {
        $post = posts::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'postingan berhasil dihapus'
        ]);
    }
}
