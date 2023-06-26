<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Tag::select('id', 'name')->get());
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function indexPostsOnTag(Tag $tag)
    {
        if (!$tag) throw new \App\Exceptions\NotFoundException(__('Not found.'));
        try {
            return response()->json($tag->posts);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }
}
