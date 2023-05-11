<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Post::with('user', 'tags')
                ->select('id', 'title',  'body', 'created_at', 'updated_at', 'user_id')
                ->withCount('comments')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2500',
            'tagNames' => 'array|max:10'
        ]);

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);


        $tags = (new TagController)->store($request->tagNames);

        $post->tags()->attach($tags);

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(
            Post::with('user:id,name,email')
                ->with('comments.user:id,name,email')
                ->select('id', 'title', 'body', 'user_id')->where('id', $id)->get()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2500',
            'tagNames' => 'array|max:10'
        ]);

        $post = Post::find($id);;
        $post->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        $tags = (new TagController)->store($request->tagNames);

        $post->tags()->sync($tags);

        return response()->json(
            [
                'message' => 'Post updated successfully',
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Post::where('id', $id)->delete();
        return response()->json(
            [
                'message' => 'Post deleted successfully',
            ]
        );
    }
}
