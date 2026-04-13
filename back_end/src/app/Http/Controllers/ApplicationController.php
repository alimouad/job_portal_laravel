<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function myApplications(Request $request): JsonResponse
    {
        $applications = Application::query()
            ->with(['job.company', 'job.category'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return response()->json($applications);
    }

    public function showMine(Request $request, int $id): JsonResponse
    {
        $application = Application::query()
            ->with(['job.company', 'job.category'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($application);
    }
}
