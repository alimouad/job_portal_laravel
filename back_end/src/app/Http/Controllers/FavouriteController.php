<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favourites = Favourite::query()
            ->with(['job.company', 'job.category'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return response()->json($favourites);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
        ]);

        $favourite = Favourite::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'job_id' => $validated['job_id'],
        ]);

        return response()->json([
            'message' => 'Job added to favourites.',
            'favourite' => $favourite,
        ], 201);
    }

    public function destroy(Request $request, int $jobId): JsonResponse
    {
        Favourite::query()
            ->where('user_id', $request->user()->id)
            ->where('job_id', $jobId)
            ->delete();

        return response()->json([
            'message' => 'Job removed from favourites.',
        ]);
    }
}
