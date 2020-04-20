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
}