<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;


class PostController extends Controller
{
    public function index()
    {
        return response()->json(Post::with('user')->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = $request->attributes->get('auth_user');

        $post = Post::create([
            'title'    => $request->title,
            'content'  => $request->content,
            'user_id'  => $user['id'],
        ]);

        return response()->json($post->load('user'), 201);
    }


}
