<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Job::with(['company', 'category'])->latest();

        if ($request->filled('q')) {
            $term = $request->string('q');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        return response()->json($query->paginate(12));
    }

    public function search(Request $request): JsonResponse
    {
        $query = Job::with(['company', 'category']);

        if ($request->filled('keyword')) {
            $term = $request->string('keyword');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }

        return response()->json($query->latest()->paginate(12));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'type' => ['required', 'in:full-time,part-time,remote,internship,freelance'],
        ]);

        $job = Job::create($validated)->load(['company', 'category']);

        return response()->json([
            'message' => 'Job created successfully.',
            'job' => $job,
        ], 201);
    }

    public function show(Job $job): JsonResponse
    {
        return response()->json($job->load(['company', 'category']));
    }

    public function update(Request $request, Job $job): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'salary' => ['sometimes', 'required', 'numeric', 'min:0'],
            'company_id' => ['sometimes', 'required', 'integer', 'exists:companies,id'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'type' => ['sometimes', 'required', 'in:full-time,part-time,remote,internship,freelance'],
        ]);

        $job->update($validated);

        return response()->json([
            'message' => 'Job updated successfully.',
            'job' => $job->fresh()->load(['company', 'category']),
        ]);
    }

    public function destroy(Job $job): JsonResponse
    {
        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully.',
        ]);
    }

    public function apply(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'cover_letter' => ['nullable', 'string'],
        ]);

        $job = Job::findOrFail($id);
        $userId = $request->user()->id;

        $alreadyApplied = Application::query()
            ->where('user_id', $userId)
            ->where('job_id', $job->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'message' => 'You have already applied for this job.',
            ], 409);
        }

        $application = Application::create([
            'user_id' => $userId,
            'job_id' => $job->id,
            'status' => 'pending',
            'cover_letter' => $validated['cover_letter'] ?? null,
        ]);

        return response()->json([
            'message' => 'Application submitted successfully.',
            'application' => $application,
        ], 201);
    }
}
