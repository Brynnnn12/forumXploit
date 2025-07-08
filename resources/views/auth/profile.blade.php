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
                            Profil: {{ $user->name }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Kerawanan:</strong> Potensi XSS pada tampilan nama dan IDOR
                        </div>

                        <div class="row">
                            <!-- Informasi Pengguna -->
                            <div class="col-md-6">
                                <div class="user-info-section">
                                    <h5 class="border-bottom pb-2">
                                        <i class="fas fa-info-circle me-2"></i>Informasi Pengguna
                                    </h5>

                                    <div class="user-detail">
                                        <span class="detail-label">ID:</span>
                                        <span class="detail-value">{{ $user->id }}</span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Nama:</span>
                                        <span class="detail-value">{!! $user->name !!}</span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Email:</span>
                                        <span class="detail-value">{{ $user->email }}</span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Peran:</span>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                            {{ $user->role }}
                                        </span>
                                    </div>

                                    <div class="user-detail">
                                        <span class="detail-label">Password:</span>
                                        <span class="text-danger">{{ $user->password }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bio Pengguna -->
                            <div class="col-md-6">
                                <div class="bio-section">
                                    <h5 class="border-bottom pb-2">
                                        <i class="fas fa-book-open me-2"></i>Biodata
                                    </h5>

                                    <div class="bio-content p-3 bg-light rounded">
                                        {!! $user->bio ?? '<em class="text-muted">Tidak ada biodata</em>' !!}
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

                            @forelse($user->posts as $post)
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
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Belum ada postingan
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Aksi -->
            <div class="col-lg-4">
                <!-- Ganti Password -->
                <div class="card shadow-sm mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>Ganti Password</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger small">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Kerawanan:</strong> Tidak ada pemeriksaan otentikasi dan CSRF
                        </div>

                        <form method="POST" action="{{ route('password.change') }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-save me-2"></i>Simpan Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tes IDOR -->
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-user-secret me-2"></i>Uji IDOR</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning small">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Kerawanan IDOR:</strong> Dapat mengakses profil pengguna lain
                        </div>

                        <form method="GET" action="{{ route('auth.profile', 1) }}" id="idorForm">
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
        // Form IDOR
        document.getElementById('idorForm').addEventListener('submit', function(e) {
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
