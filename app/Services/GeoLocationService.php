<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoLocationService
{
    /**
     * Get location information from IP address
     * Uses ip-api.com free service (45 requests per minute)
     */
    public static function getLocationFromIP($ip)
    {
        // Don't process local IPs
        if (self::isLocalIP($ip)) {
            return [
                'country' => 'Local',
                'city' => 'Local Network',
                'display' => 'Local Network'
            ];
        }

        // Check cache first (cache for 7 days)
        $cacheKey = 'ip_location_' . hash('sha256', $ip);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Use ip-api.com free tier
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=country,city,status");
            
            if ($response->successful() && $response->json('status') === 'success') {
                $data = [
                    'country' => $response->json('country', 'Unknown'),
                    'city' => $response->json('city', 'Unknown'),
                    'display' => $response->json('city', 'Unknown') . ', ' . $response->json('country', 'Unknown')
                ];
            } else {
                $data = [
                    'country' => 'Unknown',
                    'city' => 'Unknown',
                    'display' => 'Unknown Location'
                ];
            }
        } catch (\Exception $e) {
            $data = [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'display' => 'Unknown Location'
            ];
        }

        // Cache the result
        Cache::put($cacheKey, $data, now()->addDays(7));
        
        return $data;
    }

    /**
     * Check if IP is a local/private IP
     */
    private static function isLocalIP($ip)
    {
        $private_ips = array(
            '127.0.0.1',
            '192.168.0.0/16',
            '172.16.0.0/12',
            '10.0.0.0/8',
            '::1',
            'localhost'
        );

        foreach ($private_ips as $range) {
            if (stripos($range, '/') === false) {
                if ($ip === $range) return true;
            }
        }

        return false;
    }
}
