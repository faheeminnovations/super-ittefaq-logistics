<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TraccarService
{
    protected $baseUrl;
    protected $email;
    protected $password;
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        $this->baseUrl = env('TRACCAR_URL', 'http://127.0.0.1:8082');
        $this->email = env('TRACCAR_EMAIL');
        $this->password = env('TRACCAR_PASSWORD');
        $this->apiKey = env('TRACCAR_API_KEY');
        $this->client = new Client();
    }

    /**
     * Authenticate with Traccar and get session token
     */
    public function authenticate()
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/api/session", [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'email' => $this->email,
                    'password' => $this->password
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }

            Log::error('Traccar authentication failed: ' . $response->getBody());
            return null;
        } catch (\Exception $e) {
            Log::error('Traccar authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all devices from Traccar
     */
    public function getDevices()
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/devices", [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }

            Log::error('Traccar get devices failed: ' . $response->getBody());
            return [];
        } catch (\Exception $e) {
            Log::error('Traccar get devices error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get device positions from Traccar
     */
    public function getPositions()
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/positions", [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }

            Log::error('Traccar get positions failed: ' . $response->getBody());
            return [];
        } catch (\Exception $e) {
            Log::error('Traccar get positions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get latest position for a specific device
     */
    public function getDevicePosition($deviceId)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/positions?id={$deviceId}", [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $positions = json_decode($response->getBody(), true);
                return !empty($positions) ? $positions[0] : null;
            }

            Log::error('Traccar get device position failed: ' . $response->getBody());
            return null;
        } catch (\Exception $e) {
            Log::error('Traccar get device position error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get combined device and position data for live tracking
     */
    public function getLiveTrackingData()
    {
        $devices = $this->getDevices();
        $positions = $this->getPositions();

        $trackingData = [];

        foreach ($devices as $device) {
            $latestPosition = null;
            
            // Find the latest position for this device
            foreach ($positions as $position) {
                if ($position['deviceId'] == $device['id']) {
                    if (!$latestPosition || $position['fixTime'] > $latestPosition['fixTime']) {
                        $latestPosition = $position;
                    }
                }
            }

            $trackingData[] = [
                'device_id' => $device['id'],
                'name' => $device['name'],
                'unique_id' => $device['uniqueId'],
                'status' => $device['status'],
                'last_update' => $device['lastUpdate'],
                'position' => $latestPosition,
                'latitude' => $latestPosition['latitude'] ?? null,
                'longitude' => $latestPosition['longitude'] ?? null,
                'speed' => $latestPosition['speed'] ?? 0,
                'course' => $latestPosition['course'] ?? 0,
                'altitude' => $latestPosition['altitude'] ?? 0,
                'address' => $latestPosition['address'] ?? null,
                'fix_time' => $latestPosition['fixTime'] ?? null,
            ];
        }

        return $trackingData;
    }

    /**
     * Send push notification
     */
    public function sendPushNotification($deviceId, $message, $title = 'Tracking Alert')
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/api/notifications", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey
                ],
                'json' => [
                    'deviceId' => $deviceId,
                    'type' => 'command',
                    'attributes' => [
                        'title' => $title,
                        'message' => $message
                    ]
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return true;
            }

            Log::error('Traccar push notification failed: ' . $response->getBody());
            return false;
        } catch (\Exception $e) {
            Log::error('Traccar push notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send command to device
     */
    public function sendCommand($deviceId, $commandType, $attributes = [])
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/api/commands/send", [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'deviceId' => $deviceId,
                    'type' => $commandType,
                    'attributes' => $attributes
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return true;
            }

            Log::error('Traccar send command failed: ' . $response->getBody());
            return false;
        } catch (\Exception $e) {
            Log::error('Traccar send command error: ' . $e->getMessage());
            return false;
        }
    }
}