@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Post: {!! $post->title !!}</h5> {{-- XSS vulnerability --}}
                </div>
                <div class="card-body">
                    <div class="vulnerability-note">
                        <strong>Vulnerabilities:</strong> No authorization check (IDOR), no CSRF protection, XSS in form.
                    </div>

                    <form method="POST" action="{{ route('post.edit', $post->id) }}">
                        {{-- No CSRF token - vulnerability --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="{!! $post->title !!}" required> {{-- XSS vulnerability --}}
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="6" required>{!! $post->content !!}</textarea> {{-- XSS vulnerability --}}
                        </div>
                        <button type="submit" class="btn btn-primary">Update Post</button>
                        <a href="{{ route('post.show', $post->id) }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Post Actions</div>
                <div class="card-body">
                    <div class="vulnerability-note">
                        <strong>Vulnerability:</strong> No authorization check, no CSRF protection.
                    </div>

                    @if (Auth::check() && (Auth::id() == $post->user_id || Auth::user()->role === 'admin'))
                        <a href="{{ route('post.delete', $post->id) }}" class="btn btn-danger"
                            onclick="return confirm('Are you sure?')">Delete Post</a>
                    @else
                        <span class="text-muted">Tidak ada akses untuk menghapus post ini</span>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">IDOR Test</div>
                <div class="card-body">
                    <div class="vulnerability-note">
                        <strong>Vulnerability:</strong> Edit any post by changing ID.
                    </div>

                    <form method="GET" action="{{ route('post.edit', 1) }}">
                        <div class="mb-3">
                            <label for="post_id" class="form-label">Post ID</label>
                            <input type="number" class="form-control" id="post_id" name="post_id"
                                value="{{ $post->id }}" min="1">
                        </div>
                        <button type="submit" class="btn btn-warning"
                            onclick="this.form.action=this.form.action.replace(/\d+$/, document.getElementById('post_id').value)">
                            Edit Post
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
