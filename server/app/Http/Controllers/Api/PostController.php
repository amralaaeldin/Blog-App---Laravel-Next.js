<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\StoreTagsService;

class PostController extends Controller
{
    public function index()
    {
        try {
            return response()->json(
                Post::with('user', 'tags')
                    ->select('id', 'title',  'body', 'created_at', 'updated_at', 'user_id')
                    ->withCount('comments')
                    ->get()
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function show($id)
    {
        try {
            return response()->json(
                Post::with('user:id,name,email')
                    ->with('comments.user:id,name,email')
                    ->select('id', 'title', 'body', 'user_id')->findOrfail($id)
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\NotFoundException(__('Not found.'));
        }
    }

    public function store(StorePostRequest $request, StoreTagsService $storeTagsService)
    {
        try {
            $post = Post::create([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user()->id,
            ]);

            $tags = $storeTagsService->store($request->tagNames);

            $post->tags()->attach($tags);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json($post, 201);
    }

    public function update(UpdatePostRequest $request, $id, StoreTagsService $storeTagsService)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->update([
                'title' => $request->title,
                'body' => $request->body,
            ]);

            $tags = $storeTagsService->store($request->tagNames);

            $post->tags()->sync($tags);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => 'Post updated successfully',
            ]
        );
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->delete();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
        return response()->json(
            [
                'message' => 'Post deleted successfully',
            ]
        );
    }
}
