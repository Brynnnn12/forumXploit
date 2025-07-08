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
                        By {{ $post->user_id }} • {{ $post->created_at }}
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
                    <div class="vulnerability-note">
                        <strong>Vulnerability:</strong> No authentication required, content not sanitized.
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
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">SQL Injection Test</div>
                        <div class="card-body">
                            <div class="vulnerability-note">
                                <strong>Vulnerability:</strong> SQL injection in post ID parameter.
                            </div>
                            <p class="small">
                                Try these URLs:
                            </p>
                            <ul class="small">
                                <li><a href="/post/1 UNION SELECT 1,2,3,4,5,6,7,8">SQL Injection Test</a></li>
                                <li><a href="/post/1'; DROP TABLE posts; --">Destructive Query</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
@endsection
