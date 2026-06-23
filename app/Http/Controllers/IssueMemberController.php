<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueMemberController extends Controller
{
    public function store(Request $request, Issue $issue): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $issue->members()->syncWithoutDetaching([$validated['user_id']]);

        return response()->json([
            'data' => $issue->members()->orderBy('name')->get(),
        ]);
    }

    public function destroy(Issue $issue, User $user): JsonResponse
    {
        $issue->members()->detach($user->id);

        return response()->json([
            'data' => $issue->members()->orderBy('name')->get(),
        ]);
    }
}
