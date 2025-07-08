<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VulnerableController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forum routes
Route::get('/', [ForumController::class, 'index'])->name('home');
Route::get('/post/{id}', [ForumController::class, 'show'])->name('post.show');
Route::post('/post', [ForumController::class, 'store'])->name('post.store');
Route::post('/comment', [ForumController::class, 'storeComment'])->name('comment.store');
Route::post('/upload', [ForumController::class, 'upload'])->name('file.upload');

// A08: File execution endpoint
Route::post('/execute-file', [ForumController::class, 'executeFile'])->name('file.execute');

// A10: SSRF vulnerability
Route::post('/fetch-url', [ForumController::class, 'fetchUrl'])->name('fetch.url');

// A10: RCE vulnerability
Route::post('/execute-code', [ForumController::class, 'executeCode'])->name('execute.code');

// A10: XXE vulnerability
Route::post('/parse-xml', [ForumController::class, 'parseXml'])->name('parse.xml');

// Admin routes (no proper authorization check - vulnerability)
Route::get('/admin', [ForumController::class, 'admin'])->name('admin');

// A05: Security Misconfiguration - .env file exposed
Route::get('/config', function () {
    return response()->file(base_path('.env'));
})->name('config');

// A05: Debug info exposed
Route::get('/debug', function () {
    return response()->json([
        'app_key' => config('app.key'),
        'database' => config('database.connections.mysql'),
        'environment' => config('app.env'),
        'debug' => config('app.debug'),
        'secrets' => [
            'admin_password' => env('ADMIN_PASSWORD'),
            'api_key' => env('SECRET_API_KEY'),
        ]
    ]);
})->name('debug');

// A06: Vulnerable and Outdated Components
Route::get('/vulnerable-library', [VulnerableController::class, 'outdatedLibrary'])->name('vulnerable.library');
Route::post('/vulnerable-package', [VulnerableController::class, 'vulnerablePackage'])->name('vulnerable.package');

// ADDITIONAL VULNERABILITIES

// Brute Force & Authentication vulnerabilities
Route::get('/profile/{id}', [AuthController::class, 'showProfile'])->name('auth.profile');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');

// Command Injection
Route::post('/system-info', [ForumController::class, 'systemInfo'])->name('system.info');

// File Inclusion
Route::get('/include-file', [ForumController::class, 'includeFile'])->name('file.include');

// IDOR (Insecure Direct Object Reference)
Route::get('/edit-post/{id}', [ForumController::class, 'editPost'])->name('post.edit');
Route::post('/edit-post/{id}', [ForumController::class, 'editPost']);
Route::get('/delete-post/{id}', [ForumController::class, 'deletePost'])->name('post.delete');

// CSRF Attack
Route::post('/delete-user', [ForumController::class, 'deleteUser'])->name('user.delete');

// Advanced File Upload
Route::post('/upload-advanced', [ForumController::class, 'uploadAdvanced'])->name('file.upload.advanced');

// SQL Injection
Route::get('/search', [ForumController::class, 'searchPosts'])->name('posts.search');

// XSS
Route::get('/user/{id}', [ForumController::class, 'userProfile'])->name('user.profile');
