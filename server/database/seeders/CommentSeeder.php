<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        if ($users->isEmpty() || $posts->isEmpty()) {
            return;
        }

        foreach ($posts as $post) {
            // Create 2-4 main comments per post
            $commentCount = rand(2, 4);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $comment = Comment::create([
                    'user_id' => $users->random()->id,
                    'post_id' => $post->id,
                    'content' => $this->getRandomComment(),
                    'is_approved' => true,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);

                // 50% chance to add 1-2 replies to each comment
                if (rand(0, 1)) {
                    $replyCount = rand(1, 2);
                    for ($j = 0; $j < $replyCount; $j++) {
                        Comment::create([
                            'user_id' => $users->random()->id,
                            'post_id' => $post->id,
                            'parent_id' => $comment->id,
                            'content' => $this->getRandomReply(),
                            'is_approved' => true,
                            'created_at' => now()->subDays(rand(0, 25)),
                        ]);
                    }
                }
            }
        }
    }

    private function getRandomComment(): string
    {
        $comments = [
            "Great article! Really helpful insights.",
            "Thank you for sharing this valuable information.",
            "I've been looking for something like this. Perfect timing!",
            "Excellent explanation. This cleared up my confusion.",
            "Amazing content! Keep up the great work.",
            "This is exactly what I needed to know.",
            "Well written and easy to understand.",
            "Thanks for the detailed breakdown.",
            "Really appreciate you taking the time to write this.",
            "This helped me solve my problem. Thank you!",
        ];

        return $comments[array_rand($comments)];
    }

    private function getRandomReply(): string
    {
        $replies = [
            "I totally agree with your point!",
            "Thanks for the additional context.",
            "That's a great perspective.",
            "Exactly what I was thinking!",
            "You're absolutely right.",
            "Thanks for clarifying that.",
            "Good point, I hadn't considered that.",
            "I appreciate your insight.",
            "That makes perfect sense.",
            "Thanks for the follow-up!",
        ];

        return $replies[array_rand($replies)];
    }
}
