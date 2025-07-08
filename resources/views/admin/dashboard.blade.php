@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Notifikasi Kerentanan -->
        <div class="alert alert-danger mb-4">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Kerawanan Keamanan:</strong> Tidak ada pemeriksaan otorisasi - semua pengguna dapat mengakses panel
            admin
        </div>

        <!-- Ringkasan Data -->
        <div class="row">
            <!-- Daftar Pengguna -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Pengguna ({{ $users->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Peran</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="text-danger">{{ $user->password }}</span>
                                                <small class="text-muted">(plaintext)</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                                    {{ $user->role }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('auth.profile', $user->id) }}"
                                                        class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if (Auth::check() && Auth::user()->role === 'admin')
                                                        <form method="POST" action="{{ route('user.delete') }}"
                                                            class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="user_id"
                                                                value="{{ $user->id }}">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Postingan Terbaru -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Postingan Terbaru ({{ $posts->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th>Judul</th>
                                        <th>Penulis</th>
                                        <th>Dibuat</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts as $post)
                                        <tr>
                                            <td>{{ $post->id }}</td>
                                            <td>
                                                <a href="{{ route('post.show', $post->id) }}" class="text-decoration-none">
                                                    {{ Str::limit($post->title, 30) }}
                                                </a>
                                            </td>
                                            <td>{{ $post->user->name }}</td>
                                            <td>{{ $post->created_at->diffForHumans() }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if (Auth::check() && (Auth::id() == $post->user_id || Auth::user()->role === 'admin'))
                                                        <a href="{{ route('post.edit', $post->id) }}"
                                                            class="btn btn-outline-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('post.delete', $post->id) }}"
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted small">Tidak ada akses</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Kerentanan Keamanan -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bug me-2"></i>
                            Ringkasan Kerentanan Keamanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Kategori A01-A03 -->
                            <div class="col-md-4 mb-4">
                                <div class="vulnerability-category">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-lock-open me-2"></i>
                                        A01-A03: Kontrol Akses & Injeksi
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>A01:</strong> Tidak ada pemeriksaan otorisasi
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A02:</strong> Password dalam plaintext
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A03:</strong> SQL injection pada tampilan postingan
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Kategori A04-A06 -->
                            <div class="col-md-4 mb-4">
                                <div class="vulnerability-category">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-cogs me-2"></i>
                                        A04-A06: Desain & Konfigurasi
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>A04:</strong> Tidak ada validasi input
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A05:</strong> Mode debug aktif, .env terbuka
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A06:</strong> Komponen usang digunakan
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Kategori A07-A10 -->
                            <div class="col-md-4 mb-4">
                                <div class="vulnerability-category">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-user-shield me-2"></i>
                                        A07-A10: Autentikasi & Monitoring
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>A07:</strong> Tidak ada proteksi brute force
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A08:</strong> Unggah file executable
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A09:</strong> Tidak ada logging keamanan
                                        </li>
                                        <li class="list-group-item">
                                            <strong>A10:</strong> Kerentanan XSS, SSRF, RCE
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <!-- Tes Cepat -->
                            <div class="col-md-6">
                                <div class="vulnerability-tests">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-vial me-2"></i>
                                        Tes Kerentanan Cepat
                                    </h6>
                                    <div class="list-group">
                                        <a href="/config" target="_blank" class="list-group-item list-group-item-action">
                                            Lihat file .env (A05)
                                        </a>
                                        <a href="/debug" target="_blank" class="list-group-item list-group-item-action">
                                            Lihat info debug (A05)
                                        </a>
                                        <a href="/vulnerable-library" target="_blank"
                                            class="list-group-item list-group-item-action">
                                            Komponen usang (A06)
                                        </a>
                                        <a href="/post/1 UNION SELECT 1,2,3,4,5,6,7,8" target="_blank"
                                            class="list-group-item list-group-item-action">
                                            SQL Injection (A03)
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Contoh Eksploitasi -->
                            <div class="col-md-6">
                                <div class="exploit-examples">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-code me-2"></i>
                                        Contoh Eksploitasi
                                    </h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <pre class="mb-2"><strong>XSS:</strong> &lt;script&gt;alert('XSS')&lt;/script&gt;</pre>
                                            <pre class="mb-2"><strong>SSRF:</strong> http://localhost:8000/config</pre>
                                            <pre class="mb-2"><strong>RCE:</strong> echo 'PWN3D'; system('whoami');</pre>
                                            <pre class="mb-0"><strong>File Upload:</strong> Unggah file .php</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Style Tambahan -->
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .vulnerability-category h6 {
            color: #dc3545;
            font-weight: 600;
        }

        .list-group-item {
            padding: 0.75rem 1.25rem;
        }

        pre {
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            white-space: pre-wrap;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-group-sm .btn i {
            margin-right: 0;
        }
    </style>
@endsection
