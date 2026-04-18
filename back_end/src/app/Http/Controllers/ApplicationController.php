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

    public function employerApplications(Request $request): JsonResponse
    {
        $query = Application::query()
            ->with(['user', 'job.company', 'job.category'])
            ->whereHas('job.company', function ($builder) use ($request) {
                $builder->where('user_id', $request->user()->id);
            })
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->integer('job_id'));
        }

        if ($request->filled('q')) {
            $term = $request->string('q');

            $query->where(function ($builder) use ($term) {
                $builder
                    ->whereHas('job', function ($jobQuery) use ($term) {
                        $jobQuery->where('title', 'like', "%{$term}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($term) {
                        $userQuery
                            ->where('full_name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
            });
        }

        return response()->json($query->paginate(12));
    }

    public function updateEmployerApplication(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,accepted,rejected,interview'],
        ]);

        $application = Application::query()
            ->whereHas('job.company', function ($builder) use ($request) {
                $builder->where('user_id', $request->user()->id);
            })
            ->findOrFail($id);

        $application->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Application status updated successfully.',
            'application' => $application->fresh()->load(['user', 'job.company', 'job.category']),
        ]);
    }
}
