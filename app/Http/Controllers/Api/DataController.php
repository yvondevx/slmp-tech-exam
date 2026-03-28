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
        return response()->json(User::with(['address', 'company', 'posts', 'albums', 'todos'])->get());
    }

    public function posts()
    {
        return response()->json(Post::with(['user.address', 'user.company', 'comments'])->get());
    }

    public function comments()
    {
        return response()->json(Comment::with('post')->get());
    }

    public function albums()
    {
        return response()->json(Album::with(['user.address', 'user.company', 'photos'])->get());
    }

    public function photos()
    {
        return response()->json(Photo::with('album')->get());
    }

    public function todos()
    {
        return response()->json(Todo::with(['user.address', 'user.company'])->get());
    }

    public function all()
    {
        return response()->json([
            'users' => User::with(['address', 'company', 'posts', 'albums', 'todos'])->get(),
            'posts' => Post::with(['user.address', 'user.company', 'comments'])->get(),
            'comments' => Comment::with('post')->get(),
            'albums' => Album::with(['user.address', 'user.company', 'photos'])->get(),
            'photos' => Photo::with('album')->get(),
            'todos' => Todo::with(['user.address', 'user.company'])->get(),
        ]);
    }
}
