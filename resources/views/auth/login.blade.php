@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <div class="vulnerability-note">
                        <strong>Vulnerability:</strong> Passwords are stored in plaintext and compared directly.
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <a href="{{ route('register') }}" class="btn btn-link">Don't have an account?</a>
                    </form>

                    <hr>
                    <div class="mt-3">
                        <h6>Test Accounts:</h6>
                        <ul class="small">
                            <li>Admin: admin@forum.com / admin123</li>
                            <li>User: john@forum.com / password123</li>
                            <li>User: jane@forum.com / password123</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
