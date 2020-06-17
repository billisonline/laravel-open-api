<?php

namespace TestApp\Http\Controllers;

use Illuminate\Routing\Controller;
use TestApp\Post;

/**
 * Manage posts
 */
class PostController extends Controller
{
    /**
     * List posts
     *
     * @return \Illuminate\Database\Eloquent\Collection|Post[]
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Create post
     */
    public function store()
    {
        //
    }

    /**
     * Show post
     *
     * @param Post $post
     * @return Post
     */
    public function show(Post $post)
    {
        return $post;
    }

    /**
     * Update a post
     */
    public function update()
    {
        //
    }

    /**
     * Delete a post
     */
    public function destroy()
    {
        //
    }
}