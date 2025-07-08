@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <div class="container py-4">
        <div class="row">
            <!-- Kolom Profil Utama -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            Profil Pengguna: {{ $user->name }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian Keamanan:</strong> Tampilan data pengguna rentan terhadap XSS
                        </div>

                        <div class="row">
                            <!-- Informasi Pengguna -->
                            <div class="col-md-6">
                                <div class="user-info-section">
                                    <h5 class="border-bottom pb-2">
                                        <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                                    </h5>

                                    <div class="user-detail">
                                        <span class="detail-label">ID Pengguna:</span>
                                        <span class="detail-value">{{ $user->id }}</span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Nama:</span>
                                        <span class="detail-value">{!! $user->name !!}</span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Email:</span>
                                        <span class="detail-value">{!! $user->email !!}</span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Peran:</span>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                            {!! $user->role !!}
                                        </span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Bergabung Pada:</span>
                                        <span class="detail-value">{{ $user->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bio Pengguna -->
                            <div class="col-md-6">
                                <div class="bio-section">
                                    <h5 class="border-bottom pb-2">
                                        <i class="fas fa-book-open me-2"></i>Tentang Saya
                                    </h5>

                                    <div class="bio-content p-3 bg-light rounded">
                                        {!! $user->bio ?? '<em class="text-muted">Belum ada biodata</em>' !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Postingan Terbaru -->
                        <div class="recent-posts">
                            <h5 class="mb-3">
                                <i class="fas fa-file-alt me-2"></i>Postingan Terbaru
                            </h5>

                            @forelse($user->posts()->latest()->limit(5)->get() as $post)
                                <div
                                    class="post-item d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                    <div>
                                        <a href="{{ route('post.show', $post->id) }}" class="text-decoration-none">
                                            {!! $post->title !!}
                                        </a>
                                        <small class="text-muted ms-2">
                                            <i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Pengguna ini belum membuat postingan
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Aksi -->
            <div class="col-lg-4">
                <!-- Hapus Pengguna -->
                <div class="card shadow-sm mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-trash-alt me-2"></i>Hapus Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger small">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Kerentanan:</strong> Tidak ada pemeriksaan otorisasi
                        </div>

                        <form id="deleteUserForm">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Hapus Pengguna Ini
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Jelajahi Pengguna Lain -->
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Jelajahi Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning small">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Kerentanan IDOR:</strong> Dapat mengakses profil pengguna mana pun
                        </div>

                        <form method="GET" action="{{ route('user.profile', 1) }}" id="browseUsersForm">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">ID Pengguna</label>
                                <input type="number" class="form-control" id="user_id" name="user_id"
                                    value="{{ $user->id }}" min="1">
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-search me-2"></i>Tampilkan Profil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip JavaScript -->
    <script>
        // Hapus Pengguna
        document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                const formData = new FormData(this);

                fetch('{{ route('user.delete') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Pengguna berhasil dihapus');
                            window.location.href = '/';
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus pengguna');
                    });
            }
        });

        // Form Jelajahi Pengguna
        document.getElementById('browseUsersForm').addEventListener('submit', function(e) {
            const userId = document.getElementById('user_id').value;
            this.action = this.action.replace(/\d+$/, userId);
        });
    </script>

    <!-- Style Tambahan -->
    <style>
        .user-detail {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #eee;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 120px;
        }

        .detail-value {
            color: #333;
        }

        .bio-content {
            min-height: 150px;
            line-height: 1.6;
        }

        .post-item:hover {
            background-color: #f8f9fa !important;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }
    </style>
@endsection
