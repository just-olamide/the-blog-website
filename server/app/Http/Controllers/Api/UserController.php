<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Post;
use App\Models\Comment;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.platform' => ['required', 'string'],
            'social_links.*.url' => ['required', 'url'],
        ]);

        $data = $request->except('avatar');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function stats(Request $request)
    {
        $user = $request->user();

        $postsCount = Post::where('user_id', $user->id)->count();
        $totalLikes = Post::where('user_id', $user->id)->sum('like_count');
        $totalComments = Comment::whereHas('post', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        return response()->json([
            'posts' => $postsCount,
            'likes' => $totalLikes,
            'comments' => $totalComments,
        ]);
    }
}
