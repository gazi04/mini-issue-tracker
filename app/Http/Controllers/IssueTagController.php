<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueTagController extends Controller
{
    public function store(Request $request, Issue $issue): JsonResponse
    {
        $validated = $request->validate([
            'tag_id' => ['required', 'exists:tags,id'],
        ]);

        $issue->tags()->syncWithoutDetaching([$validated['tag_id']]);

        return response()->json([
            'data' => $issue->tags()->orderBy('name')->get(),
        ]);
    }

    public function destroy(Issue $issue, Tag $tag): JsonResponse
    {
        $issue->tags()->detach($tag->id);

        return response()->json([
            'data' => $issue->tags()->orderBy('name')->get(),
        ]);
    }
}
