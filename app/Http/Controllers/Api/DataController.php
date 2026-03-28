<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\Post;
use App\Models\Todo;
use App\Models\User;

class DataController extends Controller
{
    public function users()
    {
        return response()->json(User::with(['posts', 'albums', 'todos'])->get());
    }

    public function posts()
    {
        return response()->json(Post::with(['user', 'comments'])->get());
    }

    public function comments()
    {
        return response()->json(Comment::with('post')->get());
    }

    public function albums()
    {
        return response()->json(Album::with(['user', 'photos'])->get());
    }

    public function photos()
    {
        return response()->json(Photo::with('album')->get());
    }

    public function todos()
    {
        return response()->json(Todo::with('user')->get());
    }

    public function all()
    {
        return response()->json([
            'users' => User::with(['posts', 'albums', 'todos'])->get(),
            'posts' => Post::with(['user', 'comments'])->get(),
            'comments' => Comment::with('post')->get(),
            'albums' => Album::with(['user', 'photos'])->get(),
            'photos' => Photo::with('album')->get(),
            'todos' => Todo::with('user')->get(),
        ]);
    }
}
