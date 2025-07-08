@extends('layouts.app')

@section('title', 'Forum Diskusi')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Kolom Konten Utama -->
            <div class="col-md-8">
                <div class="forum-header mb-4">
                    <h2 class="fw-bold">Diskusi Terbaru</h2>
                    <p class="text-muted">Temukan berbagai topik menarik dari komunitas kami</p>
                </div>

                @foreach ($posts as $post)
                    <div class="card post-card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="post-header mb-3">
                                <h5 class="card-title fw-bold">
                                    <a href="{{ route('post.show', $post->id) }}"
                                        class="text-decoration-none">{{ $post->title }}</a>
                                </h5>
                                <div class="post-meta text-muted small">
                                    <span class="author me-2">
                                        <i class="fas fa-user"></i> {{ $post->user->name }}
                                    </span>
                                    <span class="date me-2">
                                        <i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                                    </span>
                                    @if ($post->file_path)
                                        <span class="attachment">
                                            <i class="fas fa-paperclip"></i>
                                            <a href="{{ $post->file_path }}" target="_blank"
                                                class="text-decoration-none">Lampiran</a>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="post-content card-text mb-3">
                                {!! $post->content !!}
                            </div>

                            <div class="post-footer d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">
                                    <i class="far fa-comment"></i> {{ $post->comments->count() }} komentar
                                </span>
                                <div class="post-actions">
                                    @if (Auth::check() && (Auth::id() == $post->user_id || Auth::user()->role === 'admin'))
                                        <a href="{{ route('post.edit', $post->id) }}"
                                            class="btn btn-sm btn-outline-warning me-2">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('post.delete', $post->id) }}"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Form Buat Postingan -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Buat Postingan Baru</h5>
                    </div>
                    <div class="card-body">
                        @if (Auth::check())
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Perhatian:</strong> Konten HTML diizinkan tanpa sanitasi
                            </div>

                            <form method="POST" action="{{ route('post.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul</label>
                                    <input type="text" class="form-control" id="title" name="title" required
                                        placeholder="Masukkan judul postingan">
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Isi Konten</label>
                                    <textarea class="form-control" id="content" name="content" rows="5" required
                                        placeholder="Anda bisa menggunakan HTML di sini"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="file_path" class="form-label">Lampiran File (Opsional)</label>
                                    <input type="file" class="form-control" id="file_path" name="file_path">
                                    <div class="form-text">Anda bisa mengunggah file apa saja</div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Publikasikan
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Anda harus <a href="{{ route('login') }}" class="alert-link">login</a> untuk membuat
                                postingan baru.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Panel Unggah File Terpadu -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Unggah File</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger small">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Kerentanan:</strong> Tidak ada validasi tipe file
                        </div>

                        <form id="uploadForm" action="{{ route('file.upload') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Pilih File</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                                <div class="form-text">
                                    Semua jenis file diizinkan<br>
                                    <small class="text-muted">Coba upload: .php, .exe, .js, .html, .jpg, .png</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Unggah File
                            </button>
                        </form>
                        <div id="uploadResult" class="mt-3"></div>
                    </div>
                </div>

                <!-- Panel Tes Keamanan -->
                <div class="security-tests">
                    <!-- Tes Eksekusi File -->
                    <div class="card shadow-sm mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-bug me-2"></i>Uji Eksekusi File</h5>
                        </div>
                        <div class="card-body">
                            <form id="executeForm">
                                <div class="mb-3">
                                    <label class="form-label">Path File PHP</label>
                                    <input type="text" class="form-control" id="filePath" name="path"
                                        placeholder="contoh: test.php">
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-play me-2"></i>Eksekusi
                                </button>
                            </form>
                            <div id="executeResult" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Tes SSRF -->
                    <div class="card shadow-sm mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-network-wired me-2"></i>Uji SSRF</h5>
                        </div>
                        <div class="card-body">
                            <form id="ssrfForm">
                                <div class="mb-3">
                                    <label class="form-label">URL Target</label>
                                    <input type="text" class="form-control" id="url" name="url"
                                        placeholder="http://internal/config">
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-external-link-alt me-2"></i>Fetch URL
                                </button>
                            </form>
                            <div id="ssrfResult" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip JavaScript -->
    <script>
        // Fungsi untuk menangani pengiriman form
        const handleFormSubmit = (formId, endpoint, resultId) => {
            document.getElementById(formId).addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(endpoint, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        const resultDiv = document.getElementById(resultId);

                        if (formId === 'uploadForm' && data.path) {
                            // Special handling for file upload
                            const isImage = /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(data.filename);
                            resultDiv.innerHTML = `
                                <div class="alert alert-success">
                                    <h6>File berhasil diupload!</h6>
                                    <p><strong>File:</strong> ${data.filename}</p>
                                    <p><strong>Path:</strong> <a href="${data.path}" target="_blank">${data.path}</a></p>
                                    <p><strong>Size:</strong> ${data.size} bytes</p>
                                    <p><strong>Type:</strong> ${data.mime_type}</p>
                                    ${isImage ? `<img src="${data.path}" class="img-fluid mt-2" style="max-height: 200px;" alt="Uploaded image">` : ''}
                                    <details class="mt-2">
                                        <summary>Raw Response</summary>
                                        <pre>${JSON.stringify(data, null, 2)}</pre>
                                    </details>
                                </div>
                            `;
                        } else {
                            resultDiv.innerHTML = `
                                <div class="alert alert-info">
                                    <pre>${JSON.stringify(data, null, 2)}</pre>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const resultDiv = document.getElementById(resultId);
                        resultDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <strong>Error:</strong> ${error.message}
                            </div>
                        `;
                    });
            });
        };

        // Inisialisasi handler untuk semua form
        handleFormSubmit('uploadForm', '{{ route('file.upload') }}', 'uploadResult');
        handleFormSubmit('executeForm', '/execute-file', 'executeResult');
        handleFormSubmit('ssrfForm', '/fetch-url', 'ssrfResult');
    </script>

    <style>
        .post-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .post-card:hover {
            transform: translateY(-3px);
        }

        .post-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .post-content {
            line-height: 1.6;
        }

        .security-tests .card-header {
            border-radius: 8px 8px 0 0 !important;
        }

        .post-actions .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .post-actions .btn-sm i {
            margin-right: 0.25rem;
        }
    </style>
@endsection
