<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'comments')->latest()->get();
        return view('forum.index', compact('posts'));
    }

    public function show($id)
    {
        // SQL Injection vulnerability - direct parameter injection
        $post = DB::select("SELECT * FROM posts WHERE id = $id")[0] ?? null;

        if (!$post) {
            return redirect()->route('home');
        }

        $comments = Comment::where('post_id', $id)->with('user')->get();
        return view('forum.show', compact('post', 'comments'));
    }

    public function store(Request $request)
    {
        // No validation or authorization - vulnerability
        Post::create([
            'user_id' => Auth::id() ?? 1, // Default to user 1 if not logged in
            'title' => $request->input('title'),
            'content' => $request->input('content'), // Raw HTML - XSS risk
            'file_path' => $request->input('file_path'),
        ]);

        return redirect()->route('home');
    }

    public function storeComment(Request $request)
    {
        // No validation or authorization - vulnerability
        Comment::create([
            'post_id' => $request->input('post_id'),
            'user_id' => Auth::id() ?? 1, // Default to user 1 if not logged in
            'content' => $request->input('content'), // No sanitization - XSS risk
        ]);

        return redirect()->route('post.show', $request->input('post_id'));
    }

    public function upload(Request $request)
    {
        // A08: No file integrity validation
        // A08: Executable files can be uploaded
        // A08: No file signature verification

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName(); // Original filename - vulnerability

            // A08: No file content validation
            // A08: Dangerous file extensions allowed
            $allowedExtensions = ['php', 'exe', 'bat', 'sh', 'js', 'jsp', 'asp'];
            $extension = $file->getClientOriginalExtension();

            $path = $file->storeAs('uploads', $filename, 'public'); // No extension checking

            // A08: Create executable file with dangerous content
            if ($extension === 'php') {
                $phpContent = "<?php\n// Vulnerable PHP file uploaded\necho 'File executed successfully!';\nphpinfo();\n?>";
                file_put_contents(storage_path('app/public/' . $path), $phpContent);
            }

            return response()->json([
                'path' => '/storage/' . $path,
                'filename' => $filename,
                'extension' => $extension,
                'message' => 'File uploaded successfully - executable files allowed!'
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    // A08: Direct file execution endpoint
    public function executeFile(Request $request)
    {
        $filePath = $request->input('path');
        $fullPath = storage_path('app/public/uploads/' . basename($filePath));

        if (file_exists($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
            // A08: Execute uploaded PHP file
            ob_start();
            include $fullPath;
            $output = ob_get_clean();

            return response()->json([
                'output' => $output,
                'executed' => true,
                'file' => $filePath
            ]);
        }

        return response()->json(['error' => 'File not found or not executable'], 404);
    }

    public function admin()
    {
        // No authorization check - vulnerability
        $users = User::all();
        $posts = Post::with('user')->latest()->get();
        return view('admin.dashboard', compact('users', 'posts'));
    }

    // A10: Server-Side Request Forgery (SSRF)
    public function fetchUrl(Request $request)
    {
        $url = $request->input('url');

        // A10: No URL validation - SSRF vulnerability
        // A10: Can access internal services

        try {
            $response = file_get_contents($url);
            return response()->json([
                'url' => $url,
                'content' => $response,
                'vulnerability' => 'SSRF - Can access internal services'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'url' => $url
            ], 500);
        }
    }

    // A10: Remote Code Execution via eval()
    public function executeCode(Request $request)
    {
        $code = $request->input('code');

        // A10: Direct code execution - RCE vulnerability
        ob_start();
        eval($code);
        $output = ob_get_clean();

        return response()->json([
            'code' => $code,
            'output' => $output,
            'vulnerability' => 'RCE - Direct code execution'
        ]);
    }

    // A10: XML External Entity (XXE) vulnerability
    public function parseXml(Request $request)
    {
        $xml = $request->input('xml');

        // A10: XXE vulnerability
        libxml_disable_entity_loader(false);
        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD);

        return response()->json([
            'parsed' => $dom->saveXML(),
            'vulnerability' => 'XXE - External entity processing enabled'
        ]);
    }

    // COMMAND INJECTION vulnerability
    public function systemInfo(Request $request)
    {
        $command = $request->input('command', 'whoami');

        // VULNERABILITY: Direct command execution without sanitization
        $output = shell_exec($command);

        return response()->json([
            'command' => $command,
            'output' => $output,
            'vulnerability' => 'Command Injection - Direct shell execution'
        ]);
    }

    // FILE INCLUSION vulnerability
    public function includeFile(Request $request)
    {
        $file = $request->input('file', 'config');

        // VULNERABILITY: Local File Inclusion (LFI)
        $filePath = $file . '.php';

        if (file_exists($filePath)) {
            include $filePath;
        } else {
            // VULNERABILITY: Remote File Inclusion (RFI)
            include $file;
        }

        return response()->json([
            'file' => $file,
            'included' => true,
            'vulnerability' => 'File Inclusion - LFI/RFI'
        ]);
    }

    // INSECURE DIRECT OBJECT REFERENCE (IDOR)
    public function editPost($id, Request $request)
    {
        // VULNERABILITY: No authorization check
        // Any user can edit any post

        $post = Post::find($id);
        if (!$post) {
            return redirect()->route('home')->with('error', 'Post not found');
        }

        // VULNERABILITY: No CSRF protection
        if ($request->isMethod('post')) {
            $post->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'), // XSS vulnerability
            ]);

            return redirect()->route('post.show', $id)->with('success', 'Post updated');
        }

        return view('forum.edit', compact('post'));
    }

    // INSECURE DIRECT OBJECT REFERENCE - Delete any post
    public function deletePost($id)
    {
        // VULNERABILITY: No authorization check
        // VULNERABILITY: No CSRF protection

        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return redirect()->route('home')->with('success', 'Post deleted');
        }

        return redirect()->route('home')->with('error', 'Post not found');
    }

    // CSRF ATTACK - No CSRF protection on critical actions
    public function deleteUser(Request $request)
    {
        $userId = $request->input('user_id');

        // VULNERABILITY: No CSRF token validation
        // VULNERABILITY: No authorization check

        $user = User::find($userId);
        if ($user) {
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted']);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // ADVANCED FILE UPLOAD vulnerability
    public function uploadAdvanced(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            // VULNERABILITY: No file type validation
            // VULNERABILITY: No file size validation
            // VULNERABILITY: Path traversal vulnerability

            $uploadPath = $request->input('path', 'uploads');
            $fullPath = storage_path('app/public/' . $uploadPath . '/' . $filename);

            // VULNERABILITY: Directory traversal
            $file->move(dirname($fullPath), $filename);

            // VULNERABILITY: Create .htaccess to make directory executable
            $htaccess = dirname($fullPath) . '/.htaccess';
            file_put_contents($htaccess, "Options +ExecCGI\nAddHandler cgi-script .php\n");

            return response()->json([
                'path' => $uploadPath . '/' . $filename,
                'full_path' => $fullPath,
                'vulnerability' => 'Advanced File Upload - Path traversal, executable directory'
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    // SQL INJECTION - Multiple injection points
    public function searchPosts(Request $request)
    {
        $query = $request->input('q');
        $category = $request->input('category', 'all');

        // VULNERABILITY: Multiple SQL injection points
        $sql = "SELECT * FROM posts WHERE title LIKE '%$query%'";

        if ($category !== 'all') {
            $sql .= " AND category = '$category'";
        }

        $posts = DB::select($sql);

        // Convert to collection for easier handling
        $posts = collect($posts);

        return view('forum.search', compact('posts', 'query'));
    }

    // XSS - Multiple XSS vulnerabilities
    public function userProfile($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('home');
        }

        // VULNERABILITY: XSS in user profile display
        return view('forum.profile', compact('user'));
    }
}
