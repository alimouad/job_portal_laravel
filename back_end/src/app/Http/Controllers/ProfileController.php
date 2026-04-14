<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        $user->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
        ]);

        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'photo' => $validated['photo'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh()->load('profile'),
            'profile' => $profile->fresh(),
        ]);
    }
}
