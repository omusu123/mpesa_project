<?php
/**
 * Test Callback URL Accessibility
 * This script tests if your callback URL is publicly accessible
 */

require 'config.php';

echo "========================================\n";
echo "Callback URL Accessibility Test\n";
echo "========================================\n\n";

// Get callback URL
if (defined('MPESA_CALLBACK_URL') && !empty(MPESA_CALLBACK_URL)) {
    $callbackUrl = MPESA_CALLBACK_URL;
    echo "Configured Callback URL: $callbackUrl\n\n";
} else {
    echo "⚠️  No callback URL configured in config.php\n";
    echo "Please set MPESA_CALLBACK_URL in config.php\n\n";
    exit(1);
}

echo "Testing accessibility...\n";
echo str_repeat('-', 40) . "\n\n";

// Test 1: Check if URL is valid
echo "Test 1: URL Validation\n";
$parsedUrl = parse_url($callbackUrl);
if ($parsedUrl === false) {
    echo "✗ Invalid URL format\n";
    exit(1);
}
echo "✓ URL format is valid\n";
echo "  Protocol: " . ($parsedUrl['scheme'] ?? 'none') . "\n";
echo "  Host: " . ($parsedUrl['host'] ?? 'none') . "\n";
echo "  Path: " . ($parsedUrl['path'] ?? '/') . "\n\n";

// Test 2: Check if it's not localhost
echo "Test 2: Public Accessibility Check\n";
$host = $parsedUrl['host'] ?? '';
if (in_array($host, ['localhost', '127.0.0.1', '::1']) || strpos($host, '.local') !== false) {
    echo "✗ URL uses localhost - M-Pesa cannot reach this!\n";
    echo "  You need to deploy to Render or another public server.\n";
    echo "  See RENDER_DEPLOYMENT.md for deployment instructions.\n\n";
    exit(1);
}
echo "✓ URL is not localhost\n\n";

// Test 3: Try to access the URL
echo "Test 3: HTTP Accessibility Test\n";
$ch = curl_init($callbackUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
curl_setopt($ch, CURLOPT_USERAGENT, 'M-Pesa-Callback-Test/1.0');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
$totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
curl_close($ch);

if ($curlError) {
    echo "✗ Connection failed: $curlError\n";
    echo "  The URL might not be accessible from the internet\n";
    echo "  Make sure your tunnel/server is running\n\n";
    exit(1);
}

echo "✓ Connection successful\n";
echo "  HTTP Status Code: $httpCode\n";
echo "  Response Time: " . round($totalTime * 1000, 2) . "ms\n";

if ($httpCode == 200) {
    echo "  ✓ Server responded successfully\n";
} else {
    echo "  ⚠️  Server returned status code $httpCode (this might be okay)\n";
}

echo "\nResponse Preview (first 200 characters):\n";
echo substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n\n";

// Test 4: Check if it's HTTPS (recommended)
echo "Test 4: HTTPS Check\n";
if ($parsedUrl['scheme'] === 'https') {
    echo "✓ Using HTTPS (recommended)\n\n";
} else {
    echo "⚠️  Using HTTP (HTTPS is recommended for production)\n";
    echo "  Sandbox might accept HTTP, but production requires HTTPS\n\n";
}

// Summary
echo str_repeat('=', 40) . "\n";
echo "Test Summary\n";
echo str_repeat('=', 40) . "\n";
echo "Callback URL: $callbackUrl\n";
echo "Status: ✓ ACCESSIBLE\n";
echo "\n";
echo "Your callback URL appears to be publicly accessible!\n";
echo "You can now use it for M-Pesa STK push requests.\n";
echo "\n";
echo "Next steps:\n";
echo "1. Make sure this URL is set in config.php\n";
echo "2. Run: php test_stk_push.php\n";
echo "3. Check your phone for M-Pesa prompt\n";

