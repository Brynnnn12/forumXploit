<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user (plaintext password - insecure)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@forum.com',
            'password' => 'admin123', // plaintext - insecure
            'role' => 'admin',
            'bio' => 'Administrator of the forum. <script>alert("XSS in admin bio")</script>'
        ]);

        // Create regular users (plaintext passwords - insecure)
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@forum.com',
            'password' => 'password123', // plaintext - insecure
            'role' => 'user',
            'bio' => 'Regular user bio. <img src="x" onerror="alert(\'XSS in user bio\')">'
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith <script>alert("XSS in name")</script>',
            'email' => 'jane@forum.com',
            'password' => 'password123', // plaintext - insecure
            'role' => 'user',
            'bio' => 'Another user with <b>HTML</b> content and <script>console.log("XSS executed")</script>'
        ]);

        // Create posts with potential XSS content
        $post1 = Post::create([
            'user_id' => $user1->id,
            'title' => 'Welcome to the Forum!',
            'content' => '<h2>Hello Everyone!</h2><p>This is my first post. <script>alert("XSS Test")</script></p>', // XSS risk
            'file_path' => null
        ]);

        $post2 = Post::create([
            'user_id' => $user2->id,
            'title' => 'File Upload Test',
            'content' => '<p>Testing file upload functionality</p>',
            'file_path' => '/uploads/test.php' // dangerous file extension
        ]);

        // Create comments with potential XSS content
        Comment::create([
            'post_id' => $post1->id,
            'user_id' => $user2->id,
            'content' => 'Great post! <img src="x" onerror="alert(\'XSS Comment\')">' // XSS risk
        ]);

        Comment::create([
            'post_id' => $post1->id,
            'user_id' => $admin->id,
            'content' => 'Welcome to our forum! <b>Admin message</b>'
        ]);

        Comment::create([
            'post_id' => $post2->id,
            'user_id' => $user1->id,
            'content' => 'Be careful with file uploads! <script>document.location="http://evil.com"</script>' // XSS risk
        ]);
    }
}
