<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;


class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, $postId)
    {
        try {
            return response()->json(
                Comment::create([
                    'body' => $request->body,
                    'user_id' => $request->user()->id,
                    'post_id' => $postId,
                ]),
                201
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function update(UpdateCommentRequest $request, $_, $id)
    {
        $comment = Comment::where('id', $id)->first();
        if (!$comment) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $comment->update([
                'body' => $request->body,
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Updated successfully.'),
            ]
        );
    }

    public function destroy($_, $id)
    {
        $comment = Comment::where('id', $id)->first();
        if (!$comment) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $comment->delete();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Deleted successfully.'),
            ]
        );
    }
}
