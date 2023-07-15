<?php

namespace App\Services;

use App\Models\Tag;

class StoreTagsService
{
  public function store($tagNames = [])
  {
    foreach ($tagNames as $key => $value) {
      $tagNames[$key] = ucfirst(trim($value));
    }

    try {
      $existingTags = Tag::select('id', 'name')->whereIn('name', $tagNames)->get();

      $newTags = collect($tagNames)->diff($existingTags->pluck('name'))
        ->map(function ($tagName) {
          return Tag::create(['name' => $tagName]);
        });

      $tags = $existingTags->merge($newTags)->pluck('id')->toArray();
    } catch (\Exception $e) {
      throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
    }

    return $tags;
  }
}
