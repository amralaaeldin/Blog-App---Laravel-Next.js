<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;


class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'body' => 'required|string|max:2500',
        ]);

        return response()->json(
            Comment::create([
                'body' => $request->body,
                'user_id' => $request->user()->id,
                'post_id' => $postId,
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $_, $id)
    {
        $request->validate([
            'body' => 'required|string|max:2500',
        ]);

        Comment::where('id', $id)->update([
            'body' => $request->body,
        ]);

        return response()->json(
            [
                'message' => 'Comment updated successfully',
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($_, $id)
    {
        Comment::where('id', $id)->delete();
        return response()->json(
            [
                'message' => 'Comment deleted successfully',
            ]
        );
    }
}
