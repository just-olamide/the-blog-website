<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for a specific post.
     */
    public function index(Post $post)
    {
        $comments = Comment::with(['user', 'replies' => function ($query) {
                $query->with('user')->where('is_approved', true)->orderBy('created_at', 'asc');
            }])
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'user_id' => auth('sanctum')->id(),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => true // Auto-approve for now
        ]);

        $comment->load('user');

        return response()->json($comment, Response::HTTP_CREATED);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update([
            'content' => $request->content
        ]);

        $comment->load('user');

        return response()->json($comment);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
