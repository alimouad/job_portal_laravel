<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Company::withCount('jobs')->latest()->paginate(12));
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'employer') {
            throw new AuthorizationException('Only employers can create a company.');
        }

        if (Company::where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You already have a company.',
            ], 409);
        }

        $payload = $request->all();
        $payload['size'] = $payload['size'] ?? $payload['companySize'] ?? null;

        $validated = validator($payload, [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'industry' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        $validated['user_id'] = $user->id;

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Company created successfully.',
            'company' => $company,
        ], 201);
    }

    public function show(Company $company): JsonResponse
    {
        return response()->json($company->load('jobs'));
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        if ($company->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to update this company.',
            ], 403);
        }

        $payload = $request->all();
        $payload['size'] = $payload['size'] ?? $payload['companySize'] ?? null;

        $validated = validator($payload, [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company->id)],
            'industry' => ['sometimes', 'nullable', 'string', 'max:255'],
            'size' => ['sometimes', 'nullable', 'string', 'max:255'],
            'website' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'logo' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ])->validate();

        $company->update($validated);

        return response()->json([
            'message' => 'Company updated successfully.',
            'company' => $company->fresh(),
        ]);
    }

    public function destroy(Company $company): JsonResponse
    {
        if ($company->user_id !== request()->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to delete this company.',
            ], 403);
        }

        $company->delete();

        return response()->json([
            'message' => 'Company deleted successfully.',
        ]);
    }
}
