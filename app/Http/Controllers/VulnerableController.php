<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VulnerableController extends Controller
{
    // A06: Vulnerable and Outdated Components
    // This simulates using outdated libraries with known vulnerabilities

    public function outdatedLibrary(Request $request)
    {
        // Simulate using jQuery 1.6.4 (has known XSS vulnerabilities)
        $jqueryVersion = '1.6.4';

        // Simulate using old Laravel version
        $laravelVersion = '8.0.0'; // Outdated version

        // Simulate vulnerable serialization
        $data = $request->input('data');

        // A06: Unsafe deserialization
        if ($data) {
            $unserialized = unserialize($data); // Vulnerable to object injection
        }

        return response()->json([
            'vulnerability' => 'A06: Vulnerable and Outdated Components',
            'jquery_version' => $jqueryVersion,
            'laravel_version' => $laravelVersion,
            'note' => 'Using outdated components with known vulnerabilities',
            'serialized_data' => $data ?? null
        ]);
    }

    // A06: Dependency confusion attack simulation
    public function vulnerablePackage(Request $request)
    {
        // Simulate loading from untrusted package repository
        $packageName = $request->input('package', 'fake-vulnerable-package');

        return response()->json([
            'vulnerability' => 'A06: Vulnerable Package',
            'package' => $packageName,
            'loaded_from' => 'untrusted-repository.com',
            'note' => 'Package loaded from untrusted source'
        ]);
    }
}
