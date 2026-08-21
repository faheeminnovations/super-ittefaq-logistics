<?php

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;

// Test Traccar connection
$traccarUrl = 'http://127.0.0.1:8082';
$email = 'devfaheem86@gmail.com';
$password = 'ir2Nw6!2Kh#buhv';

echo "Testing Traccar connection...\n";
echo "URL: {$traccarUrl}\n";
echo "Email: {$email}\n\n";

try {
    $client = new Client();
    
    // Test authentication
    echo "Testing authentication...\n";
    $response = $client->post("{$traccarUrl}/api/session", [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'email' => $email,
            'password' => $password
        ]
    ]);

    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Body: " . $response->getBody() . "\n\n";

    if ($response->getStatusCode() == 200) {
        echo "✓ Authentication successful!\n";
        
        // Test getting devices
        echo "\nTesting get devices...\n";
        $devicesResponse = $client->get("{$traccarUrl}/api/devices", [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        
        echo "Devices Response Status: " . $devicesResponse->getStatusCode() . "\n";
        $devices = json_decode($devicesResponse->getBody(), true);
        echo "Devices count: " . count($devices) . "\n";
        
        if (!empty($devices)) {
            echo "\nFirst device data:\n";
            print_r($devices[0]);
        }
        
        // Test getting positions
        echo "\n\nTesting get positions...\n";
        $positionsResponse = $client->get("{$traccarUrl}/api/positions", [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        
        echo "Positions Response Status: " . $positionsResponse->getStatusCode() . "\n";
        $positions = json_decode($positionsResponse->getBody(), true);
        echo "Positions count: " . count($positions) . "\n";
        
        if (!empty($positions)) {
            echo "\nFirst position data:\n";
            print_r($positions[0]);
        }
        
    } else {
        echo "✗ Authentication failed!\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}