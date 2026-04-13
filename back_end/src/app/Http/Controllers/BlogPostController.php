<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            BlogPost::with('user:id,full_name,email')->latest()->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $post = BlogPost::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Blog post created successfully.',
            'post' => $post->load('user:id,full_name,email'),
        ], 201);
    }

    public function show(BlogPost $blogPost): JsonResponse
    {
        return response()->json($blogPost->load('user:id,full_name,email'));
    }

    public function update(Request $request, BlogPost $blogPost): JsonResponse
    {
        $this->authorizeOwnership($request, $blogPost);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
        ]);

        $blogPost->update($validated);

        return response()->json([
            'message' => 'Blog post updated successfully.',
            'post' => $blogPost->fresh()->load('user:id,full_name,email'),
        ]);
    }

    public function destroy(Request $request, BlogPost $blogPost): JsonResponse
    {
        $this->authorizeOwnership($request, $blogPost);

        $blogPost->delete();

        return response()->json([
            'message' => 'Blog post deleted successfully.',
        ]);
    }

    /**
     * Only owners or admins can modify a blog post.
     */
    protected function authorizeOwnership(Request $request, BlogPost $post): void
    {
        $user = $request->user();

        if ($user->id !== $post->user_id && $user->role !== 'admin') {
            throw new AuthorizationException('You are not allowed to modify this blog post.');
        }
    }
}
