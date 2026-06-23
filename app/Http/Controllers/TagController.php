<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $tags = Tag::query()->orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $tags]);
        }

        return view('tags.index', ['tags' => $tags]);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::query()->create($request->validated());

        return response()->json(['data' => $tag], 201);
    }
}
