<?php

require_once __DIR__ . '/SierraTecnologiaMock.php';

define("MOCK_MINIMUM_VERSION", "0.56.0");

$mockStarted = false;
try {
    $mockStarted = \SierraTecnologia\SierraTecnologiaMock::start();
} catch (\Throwable $e) {
    $mockStarted = false;
}

if ($mockStarted) {
    register_shutdown_function('\SierraTecnologia\SierraTecnologiaMock::stop');

    define("MOCK_HOST", "localhost");
    define("MOCK_PORT", \SierraTecnologia\SierraTecnologiaMock::getPort());
} else {
    define("MOCK_HOST", getenv("SITEC_MOCK_HOST") ?: "localhost");
    define("MOCK_PORT", getenv("SITEC_MOCK_PORT") ?: 12111);
}

define("MOCK_URL", "http://" . MOCK_HOST . ":" . MOCK_PORT);

// Test if mock server is actually reachable
$ch = curl_init(MOCK_URL);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_NOBODY, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$resp = curl_exec($ch);
$curlErr = curl_errno($ch);
curl_close($ch);

if ($curlErr) {
    define("MOCK_OFFLINE", true);
} else {
    define("MOCK_OFFLINE", false);
}

require_once __DIR__ . '/TestCase.php';
