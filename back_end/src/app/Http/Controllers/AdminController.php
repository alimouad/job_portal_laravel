<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        return response()->json([
            'users' => User::query()->latest()->paginate(12, ['*'], 'users_page'),
            'companies' => Company::query()->latest()->paginate(12, ['*'], 'companies_page'),
            'jobs' => Job::query()->with(['company', 'category'])->latest()->paginate(12, ['*'], 'jobs_page'),
            'categories' => Category::query()->withCount('jobs')->latest()->paginate(12, ['*'], 'categories_page'),
        ]);
    }

    protected function authorizeAdmin(Request $request): void
    {
        if ($request->user()?->role !== 'admin') {
            throw new AuthorizationException('Only admins can access this section.');
        }
    }
}