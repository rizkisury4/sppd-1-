<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoController extends Controller
{
    public function searchCity(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }
        $cacheKey = 'geo_cities_id_' . md5($q);
        $results = Cache::remember($cacheKey, now()->addDay(), function () use ($q) {
            $list = $this->loadRegenciesFromGithub();
            if (empty($list)) {
                return $this->fallbackCities($q);
            }
            $qLower = mb_strtolower($q);
            $filtered = array_filter($list, function ($row) use ($qLower) {
                $name = $row['name'] ?? '';
                $alt = $row['alt_name'] ?? '';
                return str_contains(mb_strtolower($name), $qLower) || str_contains(mb_strtolower($alt), $qLower);
            });
            $mapped = array_map(function ($row) {
                // Normalisasi kapitalisasi agar rapi
                $name = ucwords(mb_strtolower($row['name']));
                return [
                    'name' => $name,
                    'lat' => (float) ($row['latitude'] ?? 0),
                    'lon' => (float) ($row['longitude'] ?? 0),
                    'type' => 'regency',
                ];
            }, array_slice(array_values($filtered), 0, 12));
            if (empty($mapped)) {
                return $this->fallbackCities($q);
            }
            return $mapped;
        });
        return response()->json($results);
    }

    private function loadRegenciesFromGithub(): array
    {
        $cacheKey = 'id_regencies_github_json_v1';
        return Cache::remember($cacheKey, now()->addDays(7), function () {
            try {
                $url = 'https://raw.githubusercontent.com/yusufsyaifudin/wilayah-indonesia/master/data/list_of_area/regencies.json';
                $resp = Http::timeout(10)->acceptJson()->get($url);
                if (!$resp->ok()) {
                    return [];
                }
                $data = $resp->json();
                return is_array($data) ? $data : [];
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    private function fallbackCities(string $q): array
    {
        $cities = [
            ['name' => 'Jakarta, Indonesia', 'lat' => -6.1753924, 'lon' => 106.8271528, 'type' => 'city'],
            ['name' => 'Bandung, Indonesia', 'lat' => -6.9174639, 'lon' => 107.6191228, 'type' => 'city'],
            ['name' => 'Surabaya, Indonesia', 'lat' => -7.2574719, 'lon' => 112.7520883, 'type' => 'city'],
            ['name' => 'Semarang, Indonesia', 'lat' => -6.966667, 'lon' => 110.416667, 'type' => 'city'],
            ['name' => 'Yogyakarta, Indonesia', 'lat' => -7.7955798, 'lon' => 110.3694896, 'type' => 'city'],
            ['name' => 'Medan, Indonesia', 'lat' => 3.5896654, 'lon' => 98.6738261, 'type' => 'city'],
            ['name' => 'Denpasar, Indonesia', 'lat' => -8.650000, 'lon' => 115.216667, 'type' => 'city'],
            ['name' => 'Makassar, Indonesia', 'lat' => -5.147665, 'lon' => 119.432731, 'type' => 'city'],
        ];
        $q = mb_strtolower($q);
        return array_values(array_filter($cities, function ($c) use ($q) {
            return str_contains(mb_strtolower($c['name']), $q);
        }));
    }
}
