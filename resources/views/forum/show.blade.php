@extends('layouts.app')

@section('title', 'View Post')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2>{{ $post->title }}</h2>
                    <div class="mb-3">
                        {!! $post->content !!} <!-- XSS vulnerability - unescaped content -->
                    </div>

                    @if ($post->file_path)
                        <div class="mb-3">
                            <strong>File Attachment:</strong>
                            <a href="{{ $post->file_path }}" target="_blank">{{ $post->file_path }}</a>
                        </div>
                    @endif

                    <small class="text-muted">
                        By {{ $post->user_id }} •
                        @php
                            try {
                                if (is_string($post->created_at)) {
                                    echo \Carbon\Carbon::parse($post->created_at)->diffForHumans();
                                } else {
                                    echo $post->created_at->diffForHumans();
                                }
                            } catch (Exception $e) {
                                echo $post->created_at ?? 'Unknown date';
                            }
                        @endphp
                    </small>

                    @if (Auth::check() && (Auth::id() == $post->user_id || Auth::user()->role === 'admin'))
                        <div class="mt-3">
                            <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-outline-warning me-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('post.delete', $post->id) }}" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">Comments</div>
                <div class="card-body">
                    @foreach ($comments as $comment)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="mb-2">
                                {!! $comment->content !!} <!-- XSS vulnerability - unescaped content -->
                            </div>
                            <small class="text-muted">
                                By {{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Add Comment</div>
                <div class="card-body">
                    @if (Auth::check())
                        <div class="vulnerability-note">
                            <strong>Vulnerability:</strong> Content not sanitized, XSS possible.
                        </div>

                        <form method="POST" action="{{ route('comment.store') }}">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <div class="mb-3">
                                <label for="content" class="form-label">Comment</label>
                                <textarea class="form-control" id="content" name="content" rows="3" required
                                    placeholder="Try: <img src='x' onerror='alert(\"XSS\")'></textarea>
                                <small class="text-muted">⚠️ HTML content is not sanitized</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Comment</button>
                            </form>
@else
    <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Anda harus <a href="{{ route('login') }}" class="alert-link">login</a> untuk menambahkan komentar.
                            </div>
    @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">SQL Injection Test</div>
                    <div class="card-body">
                        <div class="vulnerability-note">
                            <strong>Vulnerability:</strong> SQL injection in post ID parameter.
                        </div>
                        <p class="small">
                            Try these URLs (click to test):
                        </p>
                        <ul class="small">
                            <li><a href="/post/1 UNION SELECT 1,2,'Injected Title','Injected Content','',NOW(),NOW()" target="_blank">Basic UNION SELECT</a></li>
                            <li><a href="/post/1 OR 1=1" target="_blank">OR 1=1 (show first result)</a></li>
                            <li><a href="/post/1 UNION SELECT id,user_id,title,content,file_path,created_at,updated_at FROM posts WHERE id=2" target="_blank">Show post ID 2</a></li>
                            <li><a href="/post/999 UNION SELECT 1,1,'Hacked Post','<script>
                                alert(\"XSS via SQL Injection\")
                            </script>','',NOW(),NOW()" target="_blank">Inject XSS via SQL</a></li>
                            <li><a href="/post/1 UNION SELECT id,1,name,email,'password: ' || password,created_at,updated_at FROM users LIMIT 1" target="_blank">Extract user data</a></li>
                        </ul>
                        <div class="mt-3">
                            <strong>Quick Test:</strong>
                            <div class="input-group input-group-sm mt-2">
                                <input type="text" class="form-control" id="sqlTest" placeholder="Enter SQL injection" value="1 OR 1=1">
                                <button class="btn btn-outline-danger" onclick="testSQL()">Test</button>
                            </div>
                        </div>
                        <script>
                            function testSQL() {
                                const input = document.getElementById('sqlTest').value;
                                window.open('/post/' + input, '_blank');
                            }
                        </script>
                        <div class="mt-3">
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Vulnerability: Direct SQL parameter injection without sanitization
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
