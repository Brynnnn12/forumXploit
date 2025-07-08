@extends('layouts.app')

@section('title', 'Cari Postingan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Area konten utama -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Hasil pencarian untuk: <span class="text-primary">{{ $query }}</span>
                            </h4>
                            <span class="badge bg-light text-dark">{{ count($posts) }} hasil</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Kerentanan:</strong> SQL Injection pada query pencarian, XSS pada tampilan kata kunci.
                        </div>

                        @if (count($posts) > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($posts as $post)
                                    <div class="list-group-item border-0 px-0 py-3">
                                        <div class="d-flex align-items-start mb-1">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">
                                                    <a href="{{ route('post.show', $post->id) }}"
                                                        class="text-decoration-none text-dark hover-primary">{{ $post->title }}</a>
                                                </h5>
                                            </div>
                                            @if (Auth::check() && (Auth::id() == $post->user_id || Auth::user()->role === 'admin'))
                                                <div class="post-actions ms-3">
                                                    <a href="{{ route('post.edit', $post->id) }}"
                                                        class="btn btn-sm btn-outline-warning me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('post.delete', $post->id) }}"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-muted mb-2">
                                            {!! Str::limit($post->content, 200) !!}
                                        </p>
                                        <div class="d-flex align-items-center text-muted small">
                                            <span class="me-2"><i class="bi bi-person-circle me-1"></i> User
                                                #{{ $post->user_id }}</span>
                                            <span><i class="bi bi-clock me-1"></i>
                                                @php
                                                    try {
                                                        if (is_string($post->created_at)) {
                                                            echo \Carbon\Carbon::parse(
                                                                $post->created_at,
                                                            )->diffForHumans();
                                                        } else {
                                                            echo $post->created_at->diffForHumans();
                                                        }
                                                    } catch (Exception $e) {
                                                        echo $post->created_at ?? 'Unknown';
                                                    }
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">Tidak ditemukan postingan untuk "{{ $query }}"</h4>
                                <p class="text-muted">Coba gunakan kata kunci lain</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Cari Postingan</h5>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-danger mb-4">
                            <i class="bi bi-bug-fill me-2"></i>
                            <strong>Kerentanan:</strong> SQL Injection pada parameter pencarian.
                        </div>

                        <form method="GET" action="{{ route('posts.search') }}" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="q" class="form-label">Kata Kunci</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="q" name="q"
                                        value="{{ $query }}" placeholder="Cari postingan..." required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="category" name="category"
                                        placeholder="Masukkan kategori">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Cari
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="mb-3">
                            <h6 class="mb-3"><i class="bi bi-code me-2"></i>Contoh SQL Injection</h6>
                            <div class="bg-dark text-white p-3 rounded">
                                <code class="d-block text-warning mb-2">' OR 1=1 --</code>
                                <code class="d-block text-warning mb-2">' UNION SELECT 1,2,3,4,5,6,7,8 --</code>
                                <code class="d-block text-warning mb-2">' OR 1=1 LIMIT 1 --</code>
                                <code class="d-block text-danger">'; DROP TABLE posts; --</code>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Contoh di atas menunjukkan kemungkinan serangan SQL injection. Jangan pernah menggunakan input
                            user yang tidak disanitasi dalam query database.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .hover-primary:hover {
            color: #0d6efd !important;
            transition: color 0.2s ease;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .card {
            border-radius: 0.5rem;
        }

        .list-group-item {
            border-left: 0;
            border-right: 0;
        }

        .list-group-item:first-child {
            border-top: 0;
        }

        .list-group-item:last-child {
            border-bottom: 0;
        }

        .post-actions .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .post-actions .btn-sm i {
            margin-right: 0;
        }
    </style>
@endpush
