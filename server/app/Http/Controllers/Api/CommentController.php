<?php

namespace App\Http\Controllers\API;

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
    public function update(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|max:2500',
        ]);

        return response()->json(
            Comment::where('id', $id)->update([
                'body' => $request->body,
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return response()->json(Comment::where('id', $id)->delete());
    }
}
