<?php

namespace TestApp\Http\Controllers;

use Illuminate\Routing\Controller;
use TestApp\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function store()
    {
        //
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}