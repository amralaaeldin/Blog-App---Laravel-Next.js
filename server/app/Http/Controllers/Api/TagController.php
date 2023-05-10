<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Tag::select('id', 'name')->get());
    }

    public function getPostsOnTag(Tag $tag)
    {
        return response()->json($tag->posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($tagNames = [])
    {
        foreach ($tagNames as $key => $value) {
            $tagNames[$key] = ucfirst(trim($value));
        }

        $existingTags = Tag::select('id', 'name')->whereIn('name', $tagNames)->get();

        $newTags = collect($tagNames)->diff($existingTags->pluck('name'))
            ->map(function ($tagName) {
                return Tag::create(['name' => $tagName]);
            });

        $tags = $existingTags->merge($newTags)->pluck('id')->toArray();

        return $tags;
    }
}
