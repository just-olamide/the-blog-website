<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'tags'])
            ->when($request->category_id, function ($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->tag_id, function ($q) use ($request) {
                return $q->whereHas('tags', function ($q) use ($request) {
                    $q->where('id', $request->tag_id);
                });
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('content', 'like', "%{$request->search}%");
                });
            });

        if (!$request->user() || $request->public) {
            $query->published();
        } elseif ($request->user_posts) {
            $query->where('user_id', $request->user()->id);
        }

        return $query->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $post = new Post($request->except('featured_image', 'tags'));
        $post->user_id = $request->user()->id;
        $post->slug = Str::slug($request->title);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $post->featured_image = $path;
        }

        $post->save();

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return response()->json($post->load(['user', 'category', 'tags']), 201);
    }

    public function show(Post $post)
    {
        if ($post->status === 'draft' && (!request()->user() || request()->user()->id !== $post->user_id)) {
            abort(404);
        }

        $post->increment('view_count');
        
        return response()->json($post->load(['user', 'category', 'tags']));
    }

    public function update(Request $request, Post $post)
    {
        if ($request->user()->id !== $post->user_id) {
            abort(403);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('posts', 'public');
            $post->featured_image = $path;
        }

        $post->update($request->except('featured_image', 'tags'));

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return response()->json($post->load(['user', 'category', 'tags']));
    }

    public function destroy(Post $post)
    {
        if (request()->user()->id !== $post->user_id) {
            abort(403);
        }

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function like(Post $post)
    {
        $post->increment('like_count');
        return response()->json(['likes' => $post->like_count]);
    }

    public function unlike(Post $post)
    {
        $post->decrement('like_count');
        return response()->json(['likes' => $post->like_count]);
    }
}
